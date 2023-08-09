<h2>Edit Image Description</h2>
<form id="formAltText" method="post">
    <input type="hidden" id="modIID" value="<?= $_GET["i"] ?>" name="iid">
    <input type="hidden" id="modCID" value="<?= $_GET["c"] ?>" name="cid">
    <textarea name="idesc"><?= $_GET["idesc"] ?></textarea>
    <br>
    <button style="width: 100px;" id="btnSaveAltText" noLoad type="submit">Save</button>
    <button class="btnCloseModal" style="width: 100px;" type="button">Cancel</button>
</form>