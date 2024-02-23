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
        <h1><a href="./" class="text-decoration-none">🌀<?php echo $_ENV['APP_NAME']; ?></a></h1>
        <p><br/></p>
        <form class="my-5" action="" id="button-form">
            <div class="card card-body" style="overflow: auto;">
                <div class="btn-container"></div>

                <div class="bottom-container">
                    <div class="row">
                        <div class="col-2">
                            <div class="small">
                                <small class="text-muted">
                                    <?php echo(
                                    $isAlive ?
                                        '<span class="isServerAlive text-success" title="Model: ' . IG_LANGUAGE_MODEL . ' Endpoint: ' . IG_ENDPOINT . '" data-server-live="true">' .
                                        '<i class="fa-solid fa-circle-check"></i> <strong>God is listening ...</strong></span>  ' :
                                        '<span class="isServerAlive text-danger" title="Model: ' . IG_LANGUAGE_MODEL . ' Endpoint: ' . IG_ENDPOINT . '" data-server-live="false"><i class="fa-solid fa-triangle-exclamation"></i> God can´t hear you right now!</span>'
                                    ); ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-7 text-muted text-center small"><span class="info-bar "></span></div>
                        <div class="col-1"></div>
                        <div class="col-2 text-end">
                            <div class="btn-group">
                                <a href="#" title="clear achievements" class="btn text-muted" onclick="resetLocalStorage();"><i
                                            class="fa-solid fa-broom"></i></a>
                                <div class="">
                                    <a title="Settings" class="btn text-decoration-none text-secondary"
                                       type="button"
                                       data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="dropdown-item text-decoration-none"
                                               data-bs-toggle="modal"
                                               data-bs-target="#instantModelModal"><i
                                                        class="fa-solid fa-comments"></i>
                                                talk to model</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="./?do=settings"><i
                                                        class="fa-solid fa-gear"></i>
                                                Settings</a></li>

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#" onclick="resetAll();"><i
                                                        class="fa-regular fa-trash-can"></i> Reset All</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <div class="dropdown-item">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                           type="checkbox" <?php echo($bypassCache === true ? 'checked' : ''); ?>
                                                           role="switch" id="bypassCache">
                                                    <label class="form-check-label" for="bypassCache">avoid
                                                        cache</label>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="loader" style="  display: block;  background: rgba(240,240,240,0.8);">
                    <div class="loaderInner text-center" style="width: 240px;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <br/>
                        <span>🧔God is thinking ...</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!--    <div class="debug-container-fluid" style="z-index: 99999;">-->
    <!--        <a data-bs-toggle="collapse" href="#collapseDebugContainer" aria-expanded="true"-->
    <!--           aria-controls="collapseDebugContainer" class="">-->
    <!--            <i class="fa-solid fa-bug"></i></a>-->
    <!--        <div class="collapse show" id="collapseDebugContainer" style="">-->
    <!--            <hr>-->
    <!--            <div class="card card-body">-->
    <!--                <div id="traceLog"></div>-->
    <!--                    <a data-toggle="collapse" href="#collapseDebugContainer" aria-expanded="false"-->
    <!--                       aria-controls="collapseDebugContainer">close</a>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

<?php
//echo Document::renderDevContainer([1,2]);
require_once __DIR__ . '/traceContainer.php';
require_once __DIR__ . '/instantModelModal.php';

$document->end()->send();