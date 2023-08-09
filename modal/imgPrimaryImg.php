<h2>Primary Image</h2>
<form id="formPrimaryImage" method="post">
    <input type="hidden" id="modIID" value="<?= $_GET["i"] ?>" name="iid">
    <input type="hidden" id="modCID" value="<?= $_GET["c"] ?>" name="cid">
    <span>
        Set image as primary image.<br>
        Primary image will be shown<br>
        as the thumbnail for the<br>
        current Character
    </span>
    <br>
    <button id="btnSetPrimaryImg" noLoad type="submit">Set as Primary</button>
    <button class="btnCloseModal" type="button">Cancel</button>
</form>