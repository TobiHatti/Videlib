<h2>Restore Image?</h2>
<form id="formHideImage" method="post">
    <input type="hidden" id="modIID" value="<?= $_GET["i"] ?>" name="iid">
    <input type="hidden" id="modCID" value="<?= $_GET["c"] ?>" name="cid">
    <span>
        Restore image and display<br>
        it in the main gallery?
    </span>
    <br>
    <button id="btnHideImg" noLoad type="submit">Restore Image</button>
    <button class="btnCloseModal" type="button">Cancel</button>
</form>
<br><br>
<span>
    Delete image?
</span>
<form id="formDeleteImage" method="post">
    <input type="hidden" id="modIID" value="<?= $_GET["i"] ?>" name="iid">
    <input type="hidden" id="modCID" value="<?= $_GET["c"] ?>" name="cid">
    <button id="btnDeleteImage" type="submit" noLoad>Delete Image</button>
</form>