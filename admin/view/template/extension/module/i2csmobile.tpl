<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-i2csmobile" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-information" class="form-horizontal">
		
		  <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-settings" data-toggle="tab"><?php echo $tab_settings; ?></a></li>
			<li><a href="#tab-auth" data-toggle="tab"><?php echo $tab_auth; ?></a></li>
            <li><a href="#tab-analytics" data-toggle="tab"><?php echo $tab_analytics; ?></a></li>
			<li><a href="#tab-push" data-toggle="tab"><?php echo $tab_push; ?></a></li>
          </ul>
		
		
		<div class="tab-content">
			<div class="tab-pane active" id="tab-settings">
				  <!--<div class="form-group">
					<label class="col-sm-2 control-label" for="input-status">
						<span data-toggle="tooltip" title="<?php echo $help_mobile_api;?>"><?php echo $entry_mobile_api; ?></span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="i2csmobile_mobile_api" value="<?php echo $i2csmobile_mobile_api; ?>" placeholder="<?php echo $entry_mobile_api; ?>" id="entry-mobile_api" class="form-control"/>
					</div>
				  </div>-->
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-status">
						<span data-toggle="tooltip" title="<?php echo $help_mobile_user_group;?>"><?php echo $entry_mobile_user_group; ?></span>
					</label>
					<div class="col-sm-10">
					  <select name="i2csmobile_mobile_user_group" id="input-customer-group" class="form-control">
						<?php foreach ($customer_groups as $customer_group) { ?>
						<?php if ($customer_group['customer_group_id'] == $i2csmobile_mobile_user_group) { ?>
						<option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
						<?php } ?>
						<?php } ?>
					  </select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-banner">
						<span data-toggle="tooltip" title="<?php echo $help_main_banner;?>"><?php echo $entry_main_banner; ?></span>
					</label>
					<div class="col-sm-10">
					  <select name="i2csmobile_main_banner" id="input-banner" class="form-control">
						<?php foreach ($banners as $banner) { ?>
						<?php if ($banner['banner_id'] == $i2csmobile_main_banner) { ?>
						<option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
						<?php } ?>
						<?php } ?>
					  </select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-banner">
						<span data-toggle="tooltip" title="<?php echo $help_offer_banner;?>"><?php echo $entry_offer_banner; ?></span>
					</label>
					<div class="col-sm-10">
					  <select name="i2csmobile_offer_banner" id="input-banner" class="form-control">
						<?php foreach ($banners as $banner) { ?>
						<?php if ($banner['banner_id'] == $i2csmobile_offer_banner) { ?>
						<option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
						<?php } ?>
						<?php } ?>
					  </select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-banner">
						<span data-toggle="tooltip" title="<?php echo $help_offers_module_banner;?>"><?php echo $entry_offers_module_banner; ?></span>
					</label>
					<div class="col-sm-10">
					  <select name="i2csmobile_offers_module_banner" id="input-banner" class="form-control">
						<?php foreach ($banners as $banner) { ?>
						<?php if ($banner['banner_id'] == $i2csmobile_offers_module_banner) { ?>
						<option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
						<?php } ?>
						<?php } ?>
					  </select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-banner">
						<span data-toggle="tooltip" title="<?php echo $help_featured_module;?>"><?php echo $entry_featured_module; ?></span>
					</label>
					<div class="col-sm-10">
					  <select name="i2csmobile_featured_module" id="input-banner" class="form-control">
						<?php foreach ($featured_modules as $module) { ?>
						<?php if ($module['module_id'] == $i2csmobile_featured_module) { ?>
						<option value="<?php echo $module['module_id']; ?>" selected="selected"><?php echo $module['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $module['module_id']; ?>"><?php echo $module['name']; ?></option>
						<?php } ?>
						<?php } ?>
					  </select>
					</div>
				  </div>
				  <div class="form-group">
						<label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
						<div class="col-sm-10">
						  <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
						  <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
							<?php foreach ($product_categories as $product_category) { ?>
							<div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
							  <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
							</div>
							<?php } ?>
						  </div>
						</div>
				  </div>
			   </div>
			   
			<div class="tab-pane" id="tab-auth">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-status">
						<span data-toggle="tooltip" title="<?php echo $help_auth_google;?>"><?php echo $entry_auth_google;?></span>
					</label>
					<div class="col-sm-4">
						<input type="text" name="i2csmobile_auth_google_id" value="<?php echo $i2csmobile_auth_google_id; ?>" placeholder="<?php echo $entry_auth_google_id; ?>" id="entry-i2csmobile_auth_google_id" class="form-control"/>
					</div>
					<div class="col-sm-4">
						<input type="text" name="i2csmobile_auth_google_secret" value="<?php echo $i2csmobile_auth_google_secret; ?>" placeholder="<?php echo $entry_auth_google_secret; ?>" id="entry-i2csmobile_auth_google_secret" class="form-control"/>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-status">
						<span data-toggle="tooltip" title="<?php echo $help_auth_facebook;?>"><?php echo $entry_auth_facebook;?></span>
					</label>
					<div class="col-sm-4">
						<input type="text" name="i2csmobile_auth_facebook_id" value="<?php echo $i2csmobile_auth_facebook_id; ?>" placeholder="<?php echo $entry_auth_facebook_id; ?>" id="entry-i2csmobile_auth_facebook_id" class="form-control"/>
					</div>
					<div class="col-sm-4">
						<input type="text" name="i2csmobile_auth_facebook_secret" value="<?php echo $i2csmobile_auth_facebook_secret; ?>" placeholder="<?php echo $entry_auth_facebook_secret; ?>" id="entry-i2csmobile_auth_facebook_secret" class="form-control"/>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-status">
						<span data-toggle="tooltip" title="<?php echo $help_auth_twitter;?>"><?php echo $entry_auth_twitter;?></span>
					</label>
					<div class="col-sm-4">
						<input type="text" name="i2csmobile_auth_twitter_key" value="<?php echo $i2csmobile_auth_twitter_key; ?>" placeholder="<?php echo $entry_auth_twitter_key; ?>" id="entry-i2csmobile_auth_twitter_key" class="form-control"/>
					</div>
					<div class="col-sm-4">
						<input type="text" name="i2csmobile_auth_twitter_secret" value="<?php echo $i2csmobile_auth_twitter_secret; ?>" placeholder="<?php echo $entry_auth_twitter_secret; ?>" id="entry-i2csmobile_auth_twitter_secret" class="form-control"/>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="tab-analytics">
				<div class="alert alert-info">
					<?php echo $help_analytics; ?>
				</div>
			</div>

			<div class="tab-pane" id="tab-push">
				<div class="alert alert-info">
					<?php echo $help_push; ?>
				</div>
			</div>
			</div>

        </form>
      </div>
    </div>
  </div>
  
  <script type="text/javascript">
  // Category
$('input[name=\'category\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'category\']').val('');

		$('#product-category' + item['value']).remove();

		$('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
	}
});

$('#product-category').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
  </script>
  
  
</div>
<?php echo $footer; ?>