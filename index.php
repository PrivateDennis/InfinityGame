<?php

use infinityGame\Components\InfinityMaker;
use infinityGame\Controller\Document;
use infinityGame\Controller\Ollama\Client;
use infinityGame\Controller\Request;

require_once __DIR__ . '/bootstrap.php';

$do = Request::get('do');
$terms = Request::get('terms');

$bypassCache = (Request::get('bypassCache', false) === 'true');

if (!empty($do)) {

    $infinityMaker = new InfinityMaker();
    $response = $infinityMaker->resolve($terms, $bypassCache);
    if (!$response) {
        Document::toJson(['success'=>false,'error'=>InfinityMaker::$errors,'trace'=>InfinityMaker::$trace]);
    }
    Document::toJson($response);
}

$prompt = Request::get('prompt');
$response = 'Ask me!';

if (!empty($prompt)) {
    $client = new Client();
    $response = $client->generate($prompt);
}

$document = new Document();
$document->start();
?>
    <style>
        .btn-container {
            min-height: 45vh;
        }

        .btn-container .btn {
            margin: 5px;
            text-transform: capitalize;
        }

        #loader {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.4);
            overflow: hidden;
        }

        .loaderInner {
            position: relative;
            width: 30px;
            margin: auto;
            margin-top: 5%;
        }
    </style>
    <div class="container mt-2">
        <h1><a href="./" class="text-decoration-none">🌀Infinity Game</a></h1>

        <form class="mt-4" action="" id="button-form">
            <div class="card card-body">
                <div class="btn-container"></div>
                <div id="loader">
                    <div class="loaderInner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" <?php echo($bypassCache === true ? 'checked' : ''); ?>
                       role="switch" id="bypassCache">
                <label class="form-check-label" for="bypassCache">avoid cache</label>
            </div>
        </form>

        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="prompt-input"></label>
                        <textarea type="text" id="prompt-input" name="prompt" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </div>
                <div class="col">
                    <label for="raw-response"></label>
                    <textarea class="form-control" id="raw-response" readonly
                              rows="10"><?php echo $prompt . "\n" . $response; ?></textarea>
                </div>
            </div>
        </form>
    </div>


    <script>
        "use strict";
        const initialElements = [
            {term:'Fire',icon:'🔥'},
            {term:'Water',icon:'💧'},
            {term:'Earth',icon:'🌎'},
            {term:'Wind',icon:'💨'},
        ], btnClasses = {
            default: 'btn btn-sm btn-outline-secondary',
            selected: 'btn btn-sm btn-primary',
            new: 'btn btn-sm btn-success',
            brandNew: 'btn btn-sm btn-warning',
        };

        gameInit = gameInit || [];
        let termBucket = [], buttonContainer, loader;

        gameInit.push(() => {
            buttonContainer = $('.btn-container');

            const buttonClickEvent = (el) => {
                el.preventDefault();

                let button = $(el.currentTarget);
                let term = button.text();

                termBucket.push(term);
                button.attr('class', btnClasses.selected);

                if (termBucket.length > 1) {
                    loader.show();
                    $.ajax({
                        method: 'POST',
                        data: {
                            terms: termBucket,
                            do: 'askGod',
                            bypassCache: $('#bypassCache').prop('checked'),
                        }
                    }).success((res) => {

                        let className = btnClasses.new;
                        if (res.new) {
                            className = btnClasses.brandNew;
                        }


                        $('#prompt-input').val(res.prompt);
                        $('#raw-response').val(res.response + ' ' + res.icon);

                        resetButtons();
                        addButton(res.response, res.icon, className);
                        resetBuckets();
                        loader.hide();
                    }).fail((res) => {
                        console.log('Fail', res);
                        alert('Fail');

                    });
                }
            };

            const initGame = () => {
                    loader = $('#loader');
                    let l = 0;
                    for (l in initialElements) {
                        let item = initialElements[l];
                        let newButton = $(`<button class="${btnClasses.default}" data-term="${item.term}">${item.term} ${item.icon}</button>`).on('click', buttonClickEvent)
                        buttonContainer.append(newButton);
                    }

                    loader.hide();
                },
                addButton = (term, icon, className) => {

                    let found = initialElements.find((e, a) => {
                        if (e.term === term) {
                            $('.btn:contains("' + e.term + '")', buttonContainer).attr('class', btnClasses.brandNew);
                            return true;
                        }
                    });

                    if (found) {
                        return
                    }

                    className = className || btnClasses.new;

                    let newButton = $(`<button class="${className}" data-term="${term}">${term} ${icon}</button>`).on('click', buttonClickEvent)
                    buttonContainer.append(newButton);
                    initialElements.push({term,icon});
                },
                resetBuckets = () => {
                    termBucket = [];
                },
                resetButtons = () => {
                    $('.btn', buttonContainer).attr('class', btnClasses.default);
                }

            initGame();
        });
    </script>
<?php
$document->end()->send();
