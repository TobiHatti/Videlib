<h2>Delete Image?</h2>
<form id="formHideImage" method="post">
    <input type="hidden" id="modIID" value="<?= $_GET["i"] ?>" name="iid">
    <input type="hidden" id="modCID" value="<?= $_GET["c"] ?>" name="cid">
    <span>
        Image can still be viewed<br>
        in "Hidden Images". To delete<br>
        it completely, remove it from <br>
        the hidden images section
    </span>
    <br>
    <button id="btnHideImg" noLoad type="submit">Delete Image</button>
    <button class="btnCloseModal" type="button">Cancel</button>
</form>