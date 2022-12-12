<?php
$this->title = "Upload file";
?>

<audio src="" hidden="true" controls id="Sounds"></audio>

<form class="gy-2 gx-3 align-items-center p-3 border rounded">
    <div class="row">
        <div class="col-6">
            <label class="visually-hidden" for="autoSizingInput">Song Name : </label>
            <input type="text" class="form-control" id="autoSizingInput" placeholder="Author - Title">
        </div>
        <div class="col-4">
            <label class="visually-hidden" for="genres">Preference</label>
            <select class="form-select" id="genres">
                <?php $Iterator = 0; ?>
                <?php foreach($AllGenres as $val): ?>
                    <option value="<?=$val->id?>" <?=($Iterator == 0 ? "selected" : "")?>><?=$val->name?></option>
                    
                    <?php $Iterator += 1; ?>
                <?php endforeach;?>
            </select>
        </div>
        <div class="col-auto">
            <button id="ButtonUpload" type="button" onclick="form_submit()" class="disabled btn btn-primary">Загрузить</button>
        </div>
    </div>
    <label class="FileLoad" ondragover="event.preventDefault();" for="FileUpload">
        <input type="file" accept="audio/mp3, audio/wav, audio/ogg" ondragover="event.preventDefault();" onchange="open_file()" name="" id="FileUpload">
    </label>
</form>

<div id="Output">

</div>

<script src="/js/lame.min.js"></script>
<script src="/js/convert_to_mp3.js"></script>