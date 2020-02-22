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