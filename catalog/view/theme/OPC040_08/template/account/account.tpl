<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
	<h1 class="page-title"><?php echo $text_my_account; ?></h1>
	<?php echo $content_top; ?>

	  <div class="a-link-list">
	  <div class="a-link-heading"><h2><?php echo $text_my_account; ?></h2></div>
	  <div class="a-link-content">
      <ul class="list-unstyled">
        <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
        <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
        <li><a href="<?php echo $address; ?>"><?php echo $text_address; ?></a></li>
        <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
      </ul>
	  </div><!-- a-link-content END -->
	  </div>

      <?php if ($credit_cards) { ?>
	  <div class="a-link-list">
      <div class="a-link-heading"><h2><?php echo $text_credit_card; ?></h2></div>
	  <div class="a-link-content">
      <ul class="list-unstyled">
        <?php foreach ($credit_cards as $credit_card) { ?>
        <li><a href="<?php echo $credit_card['href']; ?>"><?php echo $credit_card['name']; ?></a></li>
        <?php } ?>
      </ul>
	  </div><!-- a-link-content END -->
	  </div>
      <?php } ?>
	  
	  <div class="a-link-list">
      <div class="a-link-heading"><h2><?php echo $text_my_orders; ?></h2></div>
	  <div class="a-link-content">
      <ul class="list-unstyled">
        <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
        
        
        
        <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
        <li><a href="<?php echo $recurring; ?>"><?php echo $text_recurring; ?></a></li>
      </ul>
	  </div><!-- a-link-content END -->
	  </div>
	  
	  <div class="a-link-list">
      <div class="a-link-heading"><h2><?php echo $text_my_newsletter; ?></h2></div>
	  <div class="a-link-content">
      <ul class="list-unstyled">
        <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
      </ul>
	  </div><!-- a-link-content END -->
	  </div>
	  
	<?php echo $content_bottom; ?>
	</div>
    <?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?> 