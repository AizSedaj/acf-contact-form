<?php
	function save_inquiry( $post_id ) {

		$forms = get_field('forms','option');

		$post_type = get_post_field('post_type', $post_id);


		foreach($forms as $form) {
			if($post_type == $form['post_type']) {




				$title = $form['title'];

				$title = str_replace('newpost', $post_id, $title);
				$title = do_shortcode($title);
				$post = array("post_title" => $title, 'ID' => $post_id);


				wp_update_post($post);



				$headers = array('Content-Type: text/html; charset=UTF-8');


				$subject = $title . ' (' . $post_id . ')';

				$fields = get_field_objects($post_id);

				$message = '<table class="table table-striped">';
				foreach($fields as $field) {
					if(!empty($field['value'])) {
						$message .= '<tr>';
						$message .= '<td style="padding: 5px; width: 20%" align="right" valign="top"><strong>' . $field['label'] . ': </strong></td>';

						$value = $field['value'];

						$message .= '<td  style="padding: 5px;" align="left" valign="top">';
						if($field['type'] == 'post_object') {
							foreach( $value as $post) {
								$message .= '<div>';
								$message .= $post->post_title;

								$message .= '</div>';
							}
						}

						elseif($field['type'] == 'select' && $field['multiple'] == 1) {

							foreach($value as $options) {
								$message .= '<div>';

								$message .= $options;

								$message .= '</div>';

							}

						}

						else {
							$message .= $field['value'];
						}
						$message .= '</td>';
						$message .= '</tr>';

					}
				}
				$message .= '</table>';

				if($form['no_email'] == false) {

					$recipient = $form['email'];

					$mail = wp_mail($recipient, $subject, $message, $headers );

				}

			}
		}
		return $post_id;


	}

	add_action('acf/save_post', 'save_inquiry', 20);
?>