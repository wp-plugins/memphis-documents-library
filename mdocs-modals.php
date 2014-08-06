<?php
function mdocs_load_modals() {
	load_preview_modal();
}


function load_preview_modal() {
	?>
	<div class="modal fade" id="mdocs-file-preview" tabindex="-1" role="dialog" aria-labelledby="mdocs-file-preview" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="mdocs-file-preview-body"></div>
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