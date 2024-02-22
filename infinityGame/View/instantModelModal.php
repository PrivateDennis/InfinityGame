<?php
/**
 * @var string $response
 * @var string $prompt
 */
?>
<div class="modal fade" id="instantModelModal" tabindex="-1" aria-labelledby="instantModelModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="quickPost" action="" method="post">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="instantModelModalLabel">Instant Model Test</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                        <textarea class="form-control" id="raw-response" readonly
                                  rows="10"><?php echo $prompt . "\n" . $response; ?></textarea>

<label for="prompt-input">Ask me something</label>
<textarea type="text" id="prompt-input" name="prompt" autofocus class="form-control"
          rows="2"></textarea>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Send</button>
</div>
</form>
</div>
</div>
</div>