<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-cod" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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


        <?php if (isset($error_warning) && $error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if (isset($success) && $success) { ?>
            <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-cod" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="myfatoorah_password"><?php echo $merchant_code; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="myfatoorah_merchant_code" value="<?php echo $myfatoorah_merchant_code; ?>"  class="form-control" id="myfatoorah_merchant_code" placeholder="<?php echo $merchant_code; ?>" />
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="myfatoorah_password"><?php echo $merchant_username; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="myfatoorah_merchant_username" value="<?php echo $myfatoorah_merchant_username; ?>" class="form-control" id="myfatoorah_merchant_username" placeholder="<?php echo $merchant_username; ?>" />
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="myfatoorah_password"><?php echo $merchant_password; ?></label>
                        <div class="col-sm-10">
                            <input type="password" name="myfatoorah_merchant_password" value="<?php echo $myfatoorah_merchant_password; ?>" class="form-control" id="myfatoorah_merchant_password" placeholder="<?php echo $merchant_password; ?>" />
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="">Gateway URL</label>
                        <div class="col-sm-10">
                            <input type="text" name="myfatoorah_gateway_url" value="<?php echo $myfatoorah_gateway_url; ?>" class="form-control" id="myfatoorah_gateway_url" placeholder="Gateway URL" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="myfatoorah_payment_type">Payment Type</label>
                        <div class="col-sm-10">
                            <select name="myfatoorah_payment_type" class="form-control">
                                <option value="knet" <?php echo $myfatoorah_payment_type='knet'?'selected':''; ?> >KNET</option>
                                <option value="visa" <?php echo $myfatoorah_payment_type='visa'?'selected':''; ?>>VISA</option>
                                <option value="both"<?php echo $myfatoorah_payment_type='both'?'selected':''; ?> >BOTH</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="myfatoorah_status" id="input-status" class="form-control">
                                <?php if ($myfatoorah_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-test"><?php echo $entry_test; ?></label>
                        <div class="col-sm-10">
                            <select name="myfatoorah_test" id="input-test" class="form-control">
                                <?php if ($myfatoorah_test) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-logging"><?php echo $entry_logging; ?></label>
                        <div class="col-sm-10">
                            <select name="myfatoorah_logging" id="input-logging" class="form-control">
                                <?php if ($myfatoorah_logging) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="myfatoorah_sort_order" value="<?php echo $myfatoorah_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>





                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="entry_order_status"><?php echo $entry_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="myfatoorah_order_status_id" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $myfatoorah_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                   


                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?> 