<?php
/*
 * Guia Gratis, sistema para intercambio de regalos.
 * Copyright (C) 2011  Hugo Alberto Massaroli
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#filename').change(function() {
			ajaxUpload(this.form,'/exchanges/add_photo',
				'upload_area',
				'<?php echo $this->Html->image("/img/ajax-loader.gif")?>File Uploading Please Wait...',
				'<?php echo $this->Html->image("/img/danger.png")?>Error in Upload, check settings and path info in source code.');
			return false;
		});
	});

	/*
	 * Adds an image to exchange-photo-list when added
	 */
	function ajax_upload_callback(html) {
		add_photo($('#uploaded_image_url').html(), $('#uploaded_image_id').html());
	}

	function add_photo(img_src, photo_id) {
		var html = "<li id=''>";
		html += "<input type='hidden' name='photo[]' value='"+img_src+"'>";
		html += "<img src='"+img_src+"'>";
		html += "";
		html += "<a href='/exchanges/set_default_photo/<?= $eid?>/"+photo_id+"'>Marcar como predeterminada</a>"
		html += " | ";
		html += "<a href='/exchanges/delete_photo/<?= $eid?>/"+photo_id+"'>Borrar foto</a>"
		html += "</li>";

		$('#exchange-photo-list').append(html);
	}
</script>

<?php
echo $this->Form->create('Photo',array('action'=>'/images/upload','enctype'=>'multipart/form-data'));
echo $this->Form->input('photo',array(
	'type'=>'file',
	'id'=>'filename',
	'value'=>'filename',
	'label'=>'Agregar foto',
	'name'=>'photo'
));
echo $this->Form->hidden('prefix',array('default'=>$prefix));
echo $this->Form->hidden('eid',array('default'=>$eid));
echo $this->Form->end();

?>
<div id="upload_area"></div>

<?php
	echo $this->Form->create('Photo',array('action'=>'/images/upload','enctype'=>'multipart/form-data'));
?>
<ul id="exchange-photo-list" class="exchange-photo-list">
	<?php
	if (isset($e['Exchange']['photos'])) {
	foreach ($e['Exchange']['photos'] as $photo) { ?>
	<li>
		<img alt="exchange_photo" src='<?= $photo['square']['url']?>'>
		<?php if (@!$photo['default']) { ?>
			<a href="/exchanges/set_default_photo/<?= $eid?>/<?=$photo['id']?>">Marcar como predeterminada</a> |
		<?php } else {
			echo "Foto predeterminada";
		} ?>
		<a href="/exchanges/delete_photo/<?= $eid?>/<?=$photo['id']?>">Borrar foto</a>
	</li>
	<?php }} ?>
</ul>
<?php
	echo $this->Form->end();
?>