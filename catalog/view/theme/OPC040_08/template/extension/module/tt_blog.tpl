<div id="blog_latest_new_home" class="col-sm-7">
<div class="box-heading">
<h3><a class="ttblock-heading" href="<?php echo $all_blogs; ?>"><?php echo $heading_title; ?></a></h3>
<span class="tthometab-subtittle"><?php echo $heading_sub_title; ?></span>
</div>
<div class="block_content">
<div class="customNavigation">
	<a class="btn prev ttblog_prev"></a>
	<a class="btn next ttblog_next"></a>
</div>
<ul id="ttsmartblog-carousel" class="tt-carousel ttblog-content owl-carousel owl-theme">
  <?php
	$cnt = 1;
	$tcnt_b = count($blogs);
	
?>
  <?php foreach ($blogs as $blog) { ?>
		<?php
				if($tcnt_b >= 2) {
				if($cnt % 2 == 1) {
					echo "<li class='blogli'><ul>";
				}
				}
			?>
  	<li class="blog-content item">
		<?php if ($blog['image']) { ?>
		<div class="ttblog_image_holder blog_image_holder col-sm-2">
			<span class="blog-date">
				<i class="fa fa-calendar"></i>
				<span class="ttdate"><?php echo date("j", $blog['date_added']); ?></span>
				<span class="ttmonth"><?php echo date("M", $blog['date_added']); ?></span>
				<span class="ttyear"><?php echo date("Y", $blog['date_added']); ?></span>
			</span>
			<a href="<?php echo $blog['href']; ?>">
			<img class="image_thumb" src="<?php echo $blog['image']; ?>" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" />
			<div class="blog-hover"></div>
			</a>
			<span class="bloglinks">
				<a class="icon zoom" data-lightbox="example-set" href="<?php echo $blog['image']; ?>" title="Click to view Full Image">
				<i class="fa fa-search"></i>
				</a> 
			</span>
		</div>
		<?php } ?>	
		<div class="blog-caption blog-sub-content col-sm-10">
			<h5 class="post_title"><a href="<?php echo $blog['href']; ?>"><?php echo $blog['title']; ?></a></h5>
			<p class="blog-description"><?php echo $blog['description']; ?></p>
				<div class="comment"> 
				<a href="<?php echo $blog['href']; ?>"> <i class="fa fa-comments-o"></i> <?php echo $blog['total_comments']; ?> <?php echo $entry_comment; ?></a>
				<a href="<?php echo $blog['href']; ?>" class="read-more"><?php echo $text_read_more; ?></a>
				</div>
		</div>
	</li>
    
	<?php
				if($tcnt_b >= 2) {
				if($cnt % 2 == 0) {
					echo '</li></ul>';
				}
				}

				$cnt++;
	  		?>
    <?php } // foreach END
		  if($tcnt_b > 2) { if($cnt-1 % 2 != 0) { echo '</ul></li>'; } }
	  ?>

</ul>
	<?php /* ?>
			<div class="btn-view-all">
			<button type="button" onclick="location='<?php echo $all_blogs; ?>';" class="btn btn-primary"><?php echo $button_all_blogs; ?></button>
			</div>
	<?php */ ?>
</div>
</div>
