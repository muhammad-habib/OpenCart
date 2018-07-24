<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-filter" data-toggle="tooltip"
					title="<?php echo $button_save; ?>" class="btn btn-primary">
					<i class="fa fa-save"></i>
				</button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip"
					title="<?php echo $button_cancel; ?>" class="btn btn-default"><i
					class="fa fa-reply"></i></a>
			</div>
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
    <div class="alert alert-danger">
			<i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
    <?php } ?>
    <div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post"
					enctype="multipart/form-data" id="form-mylivechat"
					class="form-horizontal">

					<table class="table table-hover">
						<tr>
							<td><label><?php echo $entry_status; ?></label></td>
							<td><select name="mylivechat_status" id="input-status"
								class="form-control">
                <?php if ($mylivechat_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
						</tr>
						<tr>
							<td style="width: 200px"><label><?php echo $entry_code; ?></label>

							</td>
							<td><input name="mylivechat_code"
								value="<?php echo $mylivechat_code; ?>" /></td>
						</tr>
						<tr>
							<td><label><?php echo $entry_displaytype; ?></label></td>
							<td><select name="mylivechat_displaytype"
								id="mylivechat_displaytype" class="form-control">
									<option value="0">Inline Chat</option>
									<option value="1" selected="selected">Chat Button</option>
									<option value="2" selected="selected">Chat Widget</option>
									<option value="3" selected="selected">Chat Box</option>
									<option value="4" selected="selected">Chat Link</option>
							</select> <script type="text/javascript">
					var displaytype = "<?php echo $mylivechat_displaytype;?>";
    				document.getElementById("mylivechat_displaytype").selectedIndex = displaytype;
				</script></td>
						</tr>
					</table>
					<div></div>
				</form>
			</div>
		</div>
	</div>
<?php echo $footer; ?>