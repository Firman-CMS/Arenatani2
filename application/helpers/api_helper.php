<?php
/*
 * Custom Helpers
 *
 */
function api_lang_helper()
{
    $ci =& get_instance();
    return $ci->language_model->get_language($ci->api_general_settings->getValueOf('site_lang'));
}

function api_langhelper($id)
{
    $ci =& get_instance();
    return $ci->language_model->get_language($id);
}

function listdataProduct($objProduct)
{
	$datas = [
		'id' => $objProduct->id,
		'title' => $objProduct->title,
		'slug' => $objProduct->slug,
		'image' => $objProduct->image,
		'penjual' => $objProduct->penjual,
		'provinsi' => $objProduct->provinsi,
		'kabupaten' => $objProduct->kabupaten,
		'kecamatan' => $objProduct->kecamatan,
		'hape' => $objProduct->hape,
		'photo_profile' => $objProduct->photo_profile,
		'product_type' => $objProduct->product_type,
		'listing_type' => $objProduct->listing_type,
		'category_id' => $objProduct->category_id,
		'subcategory_id' => $objProduct->subcategory_id,
		'third_category_id' => $objProduct->third_category_id,
		'price' => $objProduct->price / 100,
		'currency' => $objProduct->currency,
		'description' => $objProduct->description,
		'product_condition' => $objProduct->product_condition,
		'country_id' => $objProduct->country_id,
		'state_id' => $objProduct->state_id,
		'city_id' => $objProduct->city_id,
		'address' => $objProduct->address,
		'zip_code' => $objProduct->zip_code,
		'user_id' => $objProduct->user_id,
		'status' => $objProduct->status,
		'is_promoted' => $objProduct->is_promoted,
		'promote_start_date' => $objProduct->promote_start_date,
		'promote_end_date' => $objProduct->promote_end_date,
		'promote_plan' => $objProduct->promote_plan,
		'promote_day' => $objProduct->promote_day,
		'visibility' => $objProduct->visibility,
		'rating' => $objProduct->rating,
		'hit' => $objProduct->hit,
		'external_link' => $objProduct->external_link,
		'files_included' => $objProduct->files_included,
		'shipping_time' => trans($objProduct->shipping_time),
		'shipping_cost_type' => $objProduct->shipping_cost_type,
		'shipping_cost' => $objProduct->shipping_cost,
		'is_sold' => $objProduct->is_sold,
		'is_deleted' => $objProduct->is_deleted,
		'is_draft' => $objProduct->is_draft,
		'created_at' => $objProduct->created_at,
		'user_username' => $objProduct->user_username ?: '',
		'shop_name' => $objProduct->shop_name ?: $objProduct->user_username,
		'user_role' => $objProduct->user_role,
		'user_slug' => $objProduct->user_slug,
		'product_url' => base_url().$objProduct->slug,
	];

	return $datas;
}

function generateImgProduct($image, $size)
{
	if (empty($image)) {
        return base_url() . 'assets/img/no-image.jpg';
    } else {
        return base_url() . "uploads/images/" . $image->$size;
    }
}

function getFieldOptionByLang($common_id, $lang_id)
{
    $ci =& get_instance();
    return $ci->field_model->get_field_option_by_lang($common_id, $lang_id);
}

function getCustomFieldValue($custom_field, $selected_lang)
{
    $str = "";
    if (!empty($custom_field)) {
        if (!empty($custom_field->field_value)) {
            $str = html_escape($custom_field->field_value);
        } elseif (!empty($custom_field->field_common_ids)) {
            foreach ($custom_field->field_common_ids as $item) {
                $field_option = getFieldOptionByLang($item, $selected_lang);
                if (!empty($field_option)) {
                    if (empty($str)) {
                        $str = $field_option->field_option;
                    } else {
                        $str .= ", " . $field_option->field_option;
                    }
                }
            }
        }
    }
    return $str;
}

function getCustomFieldValue2($custom_field, $selected_lang)
{
    $str = "";
    if (!empty($custom_field)) {
        if (!empty($custom_field->field_value)) {
            $str = html_escape($custom_field->field_value);
        } elseif (!empty($custom_field->field_common_ids)) {
            foreach ($custom_field->field_common_ids as $item) {
                $field_option = getFieldOptionByLang($item, $selected_lang);
                if (!empty($field_option)) {
                    if (empty($str)) {
                        $str['unit'] = $field_option->field_option;
                        $str['code'] = $field_option->common_id;
                    } else {
                        $str['unit'] .= ", " . $field_option->field_option;
                        $str['code'] .= ", " . $field_option->common_id;
                    }
                }
            }
        }
    }
    return $str;
}

function getLocation($object)
{
    $ci =& get_instance();
    $location = "";
    if (!empty($object)) {
        if (!empty($object->address)) {
            $location = $object->address;
        }
        if (!empty($object->zip_code)) {
            $location .= " " . $object->zip_code;
        }
        if (!empty($object->city_id)) {
            $city = $ci->location_model->get_city($object->city_id);
            if (!empty($city)) {
                if (!empty($object->address) || !empty($object->zip_code)) {
                    $location .= " ";
                }
                $location .= $city->name;
            }
        }
        if (!empty($object->state_id)) {
            $state = $ci->location_model->get_state($object->state_id);
            if (!empty($state)) {
                if (!empty($object->address) || !empty($object->zip_code) || !empty($object->city_id)) {
                    $location .= ", ";
                }
                $location .= $state->name;
            }
        }
        if (!empty($object->country_id)) {
            $country = $ci->location_model->get_country($object->country_id);
            if (!empty($country)) {
                if (!empty($object->state_id) || $object->city_id || !empty($object->address) || !empty($object->zip_code)) {
                    $location .= ", ";
                }
                $location .= $country->name;
            }
        }
    }
    return $location;
}

function getAvatar($user)
{
	if (!empty($user->avatar) && file_exists(FCPATH . $user->avatar)) {
	    return base_url() . $user->avatar;
	} elseif (!empty($user->avatar) && $user->user_type != "registered") {
	    return base_url() . $user->avatar;
	} else {
	    return base_url() . "assets/img/user.png";
	}
}

function timeAgo($timestamp)
{
    $time_ago = strtotime($timestamp);
    // $current_time = strtotime('-12 hour',time());
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);           // value 60 is seconds
    $hours = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec
    $days = round($seconds / 86400);          //86400 = 24 * 60 * 60;
    $weeks = round($seconds / 604800);          // 7*24*60*60;
    $months = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60
    $years = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60
    if ($seconds <= 60) {
        return trans("just_now");
    } else if ($minutes <= 60) {
        if ($minutes == 1) {
            return "1 " . trans("minute_ago");
        } else {
            return "$minutes " . trans("minutes_ago");
        }
    } else if ($hours <= 24) {
        if ($hours == 1) {
            return "1 " . trans("hour_ago");
        } else {
            return "$hours " . trans("hours_ago");
        }
    } else if ($days <= 30) {
        if ($days == 1) {
            return "1 " . trans("day_ago");
        } else {
            return "$days " . trans("days_ago");
        }
    } else if ($months <= 12) {
        if ($months == 1) {
            return "1 " . trans("month_ago");
        } else {
            return "$months " . trans("months_ago");
        }
    } else {
        if ($years == 1) {
            return "1 " . trans("year_ago");
        } else {
            return "$years " . trans("years_ago");
        }
    }
}

function userDataList($userObj)
{
	$data = [
		'id' => $userObj->id,
		'username' => $userObj->username,
		'slug' => $userObj->slug,
		'shop_name' => $userObj->shop_name ?: $userObj->username,
	];

	return $data;
}

function getProductImageUrl($image, $size)
{
    if ($image) {
        return base_url() . "uploads/images/" . $image->$size;
    } else {
        return base_url() . 'assets/img/no-image.jpg';
    }
}