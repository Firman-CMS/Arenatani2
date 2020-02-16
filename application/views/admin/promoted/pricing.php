<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Jual barang 5x lebih cepat,promosikan sekarang!</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            <?php echo form_open('product_admin_controller/promoted_products_pricing_post'); ?>
            <div class="box-body">
                <!-- include message block -->
                <?php $this->load->view('admin/includes/_messages'); ?>
                <div class="form-group">
                    <label class="control-label"><?php echo trans('price_per_day'); ?></label>
                    <input type="text" name="price_per_day" class="form-control form-input price-input" value="<?php echo price_format_input($payment_settings->price_per_day); ?>" onpaste="return false;" maxlength="32" required>
                </div>
				<div class="form-group">
                    <label class="control-label">Harga promosi per minggu</label>
                    <input type="text" name="price_per_week" class="form-control form-input price-input" value="<?php echo price_format_input($payment_settings->price_per_week); ?>" onpaste="return false;" maxlength="32" required>
                </div>
				
                <div class="form-group">
                    <label class="control-label"><?php echo trans('price_per_month'); ?></label>
                    <input type="text" name="price_per_month" class="form-control form-input price-input" value="<?php echo price_format_input($payment_settings->price_per_month); ?>" onpaste="return false;" maxlength="32" required>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right"><?php echo trans('save_changes'); ?></button>
            </div>
            <!-- /.box-footer -->
            <!-- /.box -->
            <?php echo form_close(); ?><!-- form end -->
        </div>
    </div>
</div>