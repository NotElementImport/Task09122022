<?php

use yii\helpers\Url;

$this->title = "Home";

$Filter = $_GET['filter'];

?>

<label class="visually-hidden" for="genres"> Фильтр: </label>
<select class="form-select" id="genres" onchange="document.location = '<?=Url::toRoute(['/', 'page' => $_GET['page']])?>&filter='+this.options[this.selectedIndex].value;">
    <option value="0" <?=($Filter==0?"selected":"")?>>Все</option>

    <? $Iterator = 0; ?>

    <?php foreach($AllGenres as $val): ?>
        <option value="<?=$val->id?>" <?=($Filter==$val->id?"selected":"")?>><?=$val->name?></option>
        
        <? $Iterator += 1; ?>
    <?php endforeach;?>
</select>

<div class="row">
    <div class="col-md-9" id="track_playlist">
        Test
    </div>
    <div class="col-md-auto">
        Просто для красоты.<br>Тут могла быть ваша реклама сёмги.
    </div>
</div>

<nav aria-label="Page navigation align-items-center">
  <ul class="pagination justify-content-center">
    <?php if ($PageCount != 0) : ?>
        <?php for($i = 0; $i < 3; $i++) : ?>
            <?php
            $CurrentPage = $_GET['page'];
            $Number = 1;

            if($CurrentPage == 1)
            {
                $Number = 1 + $i;
            }
            elseif ($CurrentPage == $PageCount)
            {
                $Number = $PageCount - 2 + $i;
            }
            else
            {
                $Number = $CurrentPage - 1 + $i;
            }

            $Render = true;
            if($Number > $PageCount)
            {
                $Render = false;
            }

            $Active = "";
            if($Number == $CurrentPage)
            {
                $Active = "active";
            }
            ?>

            <?php if ($Render) : ?>
                <li class="page-item <?=$Active?>">
                    <a class="page-link" href="<?=Url::toRoute(['/', 'page' => $Number, 'filter' => $_GET['filter']])?>"><?=$Number?></a>
                </li>
            <?php endif; ?>
        <?php endfor; ?>
    <?php endif; ?>
  </ul>
</nav>

<script src="https://unpkg.com/wavesurfer.js"></script>
<script src="/js/home.js"></script>
<script>
    TryCreateQueryToLoadTracks(<?= $_GET['page']?> - 1);
</script>