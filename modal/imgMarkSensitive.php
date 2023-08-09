<form id="formToggleSensitivity" method="post">
    <input type="hidden" id="modIID" value="<?= $_GET["i"] ?>" name="iid">
    <input type="hidden" id="modCID" value="<?= $_GET["c"] ?>" name="cid">
    <?php if($_GET["s"] == "true"): ?>
        <h2>Mark as Safe?</h2>
        <span>
            Safe/SFW images will be<br>
            shown by without blur.
        </span>
        <br>
        <button id="btnToggleSensitive" noLoad type="submit">Mark as Safe</button>
    <?php else: ?>
        <h2>Mark as Sensitive?</h2>
        <span>
            Sensitive/NSFW images will be<br>
            blurred by default.
        </span>
        <br>
        <button id="btnToggleSensitive" noLoad type="submit">Mark as Sensitive</button>
    <?php endif; ?>

    <button type="button" class="btnCloseModal">Cancel</button>
</form>