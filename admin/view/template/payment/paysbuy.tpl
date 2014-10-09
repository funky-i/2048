<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-pp-std-uk" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if (isset($error['error_warning'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-pp-std-uk" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-status" data-toggle="tab"><?php echo $tab_status; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">

              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-id"><?php echo $entry_id; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="paysbuy_id" value="<?php echo $paysbuy_id; ?>" placeholder="<?php echo $entry_id; ?>" id="input-id" class="form-control"/>
                  <?php if ($error_id) { ?>
                  <div class="text-danger"><?php echo $error_id; ?></div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-username"><?php echo $entry_username; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="paysbuy_username" value="<?php echo $paysbuy_username; ?>" placeholder="<?php echo $entry_username; ?>" id="input-username" class="form-control"/>
                  <?php if ($error_username) { ?>
                  <div class="text-danger"><?php echo $error_username; ?></div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-securecode"><?php echo $entry_securecode; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="paysbuy_securecode" value="<?php echo $paysbuy_securecode; ?>" placeholder="<?php echo $entry_securecode; ?>" id="input-securecode" class="form-control"/>
                  <?php if ($error_securecode) { ?>
                  <div class="text-danger"><?php echo $error_securecode; ?></div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-test"><span data-toggle="tooltip" title="<?php echo $help_test; ?>"><?php echo $entry_test; ?></span></label>
                <div class="col-sm-10">
                  <select name="paysbuy_test" id="input-test" class="form-control">
                    <?php if ($paysbuy_test) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_language; ?></label>
                <div class="col-sm-10">
                  <select name="paysbuy_language" id="input-language" class="form-control">     
                  <?php foreach ($languages as $language) { ?>
                    <?php if ($language['language_id'] == $paysbuy_language) { ?>
                      <option value="<?php echo $language['language_id']; ?>" selected="selected"><?php echo $language['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="paysbuy_total" value="<?php echo $paysbuy_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control"/>                  
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-currency"><?php echo $entry_currency; ?></label>
                <div class="col-sm-10">
                  <select name="paysbuy_currency" id="input-currency" class="form-control">     
                  <?php foreach ($currencies as $currency) { ?>
                    <?php if ($currency['currency_id'] == $paysbuy_currency) { ?>
                      <option value="<?php echo $currency['currency_id']; ?>" selected="selected"><?php echo $currency['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $currency['currency_id']; ?>"><?php echo $currency['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="paysbuy_sort_order" value="<?php echo $paysbuy_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control"/>                  
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="paysbuy_status" id="input-status" class="form-control">
                    <?php if ($paysbuy_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

            </div>

            <div class="tab-pane" id="tab-status">

              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_order_status; ?></label>
                <div class="col-sm-10">
                  <select name="paysbuy_order_status_id" class="form-control">     
                  <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $paysbuy_order_status_id) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_fail_status; ?></label>
                <div class="col-sm-10">
                  <select name="paysbuy_fail_status_id" class="form-control">     
                  <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $paysbuy_fail_status_id) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_process_status; ?></label>
                <div class="col-sm-10">
                  <select name="paysbuy_process_status_id" class="form-control">     
                  <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $paysbuy_process_status_id) { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                </div>
              </div>

            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>