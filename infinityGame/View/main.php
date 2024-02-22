<?php
/**
 * @var bool $bypassCache
 * @var array $clientOptions
 */

use infinityGame\Controller\Document;
use infinityGame\Controller\Ollama\Client;


/** wake up client */
$client = new Client();
$isAlive = $client->isAlive();

$document = new Document();
$document->start();
?>
    <div class="container my-2">
        <h1><a href="./" class="text-decoration-none">🌀<?php echo $_ENV['APP_NAME'];?></a></h1>
        <p>Select two items - can also be the same to craft and create infinite new elements.</p>
        <form class="my-5" action="" id="button-form">
            <div class="card card-body">
                <div class="btn-container"></div>
                <div class="text-end small">
                    <small class="text-muted">
                        <?php echo(
                        $isAlive ?
                            '<span class="isServerAlive text-success" data-server-live="true"><strong>Server is up and running.</strong><br/>' .
                            '<strong>Model:</strong> ' . IG_LANGUAGE_MODEL . '<br/><strong>Endpoint:</strong>: ' . IG_ENDPOINT . '</span>  ' :
                            '<span class="isServerAlive text-danger" data-server-live="false">Server is down!</span>'
                        ); ?>
                    </small>
                </div>
                <div id="loader">
                    <div class="loaderInner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                               type="checkbox" <?php echo($bypassCache === true ? 'checked' : ''); ?>
                               role="switch" id="bypassCache">
                        <label class="form-check-label" for="bypassCache">avoid cache</label>
                    </div>
                </div>
                <div class="col text-end"><a href="#" class="text-decoration-none" data-bs-toggle="modal"
                                             data-bs-target="#instantModelModal"><i class="fa-solid fa-comments"></i> talk to model</a></div>
            </div>
        </form>
    </div>
<?php

require_once __DIR__ . '/instantModelModal.php';

$document->end()->send();