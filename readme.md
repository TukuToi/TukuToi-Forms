=== Plugin Name ===
Contributors: beda.s
Donate link: https://www.tukutoi.com/
Tags: comments, spam
Requires at least: 5.7
Tested up to: 5.7
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Install like any WP Plugin

Insert in any page a shortcode like `[tkt_post_form roles_allowlist="administrator" edit_mode="true" edit_others="false" no_access_message="NO!"]` and then call the page with URL paramters `/?post_id=88` where 88 is a post to edit, or use shortcde with no `edit_mode="true"` and call Page with `/?type=post` where `post` is the post type to create a new Post.

To add fields to the form which by default is empty, you can use the filter like below example:
```
add_filter( 'tkt_form_fields', 'additional_fields', 10, 3 );
function additional_fields($fields, $id, $data){
	ob_start();
	?>
	<label for="postform_post_title">Post Title</label>
	<input type="text" name="postform_post_title" id="postform_post_title" placeholder="Here Goes The New Post Title" value="<?php echo get_post($id)->post_title ?>"/>
	<label for="postform_post_content">Post Body</label>
	<textarea id="postform_post_content" name="postform_post_content" rows="4" cols="50" placeholder="post body"><?php echo get_post($id)->post_content ?></textarea>
	<?php
	return ob_get_clean();
}
```

Note that currently only 2 field types are supported `postform_post_title` and `postform_post_content` (post Title and Post Content)
However of course future iterations will add support for all post, user and term object standard inputs and as well custo inputs.

After this, the form can be used to edit or create posts at the moment (users and terms cannot yet be created althoug classes are ready)

Biggest challenge remains to add and listen to ShortCode attributes - currently only URL parameter (to set post and type) can be used.
