<?php if ($testmode) { ?>  
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $text_test_mode; ?></div>
<?php } ?>

<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="psb" value="<?php echo $psbid; ?>" />
  <input Type="Hidden" Name="biz" value="<?php echo $username; ?>"/>
  <input type="hidden" name="currencyCode" value="<?php echo $currencyCode; ?>" />
  <input Type="Hidden" Name="inv" value="<?php echo $custom; ?>"/>
  <input type="hidden" name="postURL" value="<?php echo $resp_front_url; ?>" />
  <input type="hidden" name="reqURL" value="<?php echo $resp_back_url; ?>" />
  <input Type="Hidden" Name="itm" value="<?php echo $invoice; ?>"/>
  <input Type="Hidden" Name="amt" value="<?php echo $total; ?>"/>

  <div class="buttons">
    <div class="pull-right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>