<?php
function mdocs_load_modals() {
	load_preview_modal();
	load_ratings_modal();
	load_add_update_modal();
	load_share_modal();
	load_description_modal();
}
function load_add_update_modal() {
	?>
	<div class="modal fade" id="mdocs-add-update" tabindex="-1" role="dialog" aria-labelledby="mdocs-add-update" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','mdocs'); ?></span></button>
					<div class="mdocs-add-update-body">
						<?php mdocs_uploader(); ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_description_modal() {
	?>
	<div class="modal fade" id="mdocs-description-preview" tabindex="-1" role="dialog" aria-labelledby="mdocs-description-preview" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','mdocs'); ?></span></button>
					<div class="mdocs-description-preview-body mdocs-modal-body"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_preview_modal() {
	?>
	<div class="modal fade" id="mdocs-file-preview" tabindex="-1" role="dialog" aria-labelledby="mdocs-file-preview" aria-hidden="true" >
		<div class="modal-dialog modal-lg" style="height: 100% !important;">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','mdocs'); ?></span></button>
					<div class="mdocs-file-preview-body mdocs-modal-body"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function load_ratings_modal() {
	?>
	<div class="modal fade" id="mdocs-rating" tabindex="-1" role="dialog" aria-labelledby="mdocs-ratings" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','mdocs'); ?></span></button>
					<div class="mdocs-ratings-body mdocs-modal-body"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function load_share_modal() {
	?>
	<div class="modal fade" id="mdocs-share" tabindex="-1" role="dialog" aria-labelledby="mdocs-share" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php _e('Close','mdocs'); ?></span></button>
					<div class="mdocs-share-body mdocs-modal-body"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>