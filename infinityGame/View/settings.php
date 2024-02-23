<?php

use infinityGame\Controller\Document;
use infinityGame\Controller\FileHandler;
use infinityGame\Controller\Ollama\Client;

$client = new Client();
$endpoint = Client::getUri('/tags');
$modelList = json_decode(file_get_contents($endpoint), 1);

$table = '<tr><td colspan="3">No Models found</td></tr>';
if (isset($modelList['models']) && is_array($modelList['models']) && count($modelList['models']) > 0) {

    $table = '';
    foreach ($modelList['models'] as $modelItem) {

        $modified = date('Y-m-d', strtotime($modelItem['modified_at']));

        $table .= '<tr data-model="' . $modelItem['name'] . '" onclick="selectRow(this);" class="' . (IG_LANGUAGE_MODEL === $modelItem['name'] ? 'table-info' : '') . '">' .
            '<th>' . $modelItem['name'] . '</th>' .
            '<td>' . $modified . '</td>' .
            '<td>' . FileHandler::formatBytes($modelItem['size']) . '</td>' .
            '</tr>';
    }

}


$document = new Document();
$document->start();
?>
    <div class="container my-2">
        <h1><a href="./" class="text-decoration-none">🌀<?php echo $_ENV['APP_NAME']; ?></a></h1>
        <p><br/></p>
        <div class="row">
            <div class="col">
                <div class="card card-body">
                    <h3>Your current configuration</h3>
                    <hr>
                    <form action="./" method="post">
                        <div class="mb-3">
                            <label for="appName" class="form-label">App Name</label>
                            <input type="text" class="form-control" id="appName" name="APP_NAME"
                                   value="<?php echo $_ENV['APP_NAME']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="appName" class="form-label">App Version</label>
                            <input type="text" class="form-control" id="appName" name="APP_VERSION"
                                   value="<?php echo $_ENV['APP_VERSION']; ?>">
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="apiLanguageModel" class="form-label">Language Model</label>
                            <input type="text" class="form-control" id="apiLanguageModel" name="IG_LANGUAGE_MODEL"
                                   value="<?php echo $_ENV['IG_LANGUAGE_MODEL']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="apiEndpoint" class="form-label">Api Endpoint</label>
                            <input type="text" class="form-control" id="apiEndpoint" name="IG_ENDPOINT"
                                   value="<?php echo $_ENV['IG_ENDPOINT']; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="apiPath" class="form-label">Api Path</label>
                            <input type="text" class="form-control" id="apiPath" name="IG_PATH"
                                   value="<?php echo $_ENV['IG_PATH']; ?>">
                        </div>
                        <hr>
                        <input type="hidden" name="do" value="saveSettings">
                        <button class="btn btn-primary">Apply changes</button>
                    </form>
                </div>
            </div>
            <div class="col">
                <div class="card card-body">
                    <h3>Your Model-List</h3>
                    <hr>
                    <table class="table table-hover" id="modelTable">
                        <thead>
                        <tr>
                            <th>Model</th>
                            <th>Last Modified</th>
                            <th>Size</th>
                        </tr>
                        </thead>
                        <?php echo $table; ?>
                    </table>

                    <a href="./" class="btn btn-secondary">Back</a>

                </div>

                <div class="mt-3">
                <h3>Reset</h3>
                <hr>
                <ul class="">
                    <li><a class="dropdown-item" href="./?do=reset-cache"
                           onclick="return confirm('Are you sure, you want to delete the entire cache?')"><i
                                    class="fa-regular fa-trash-can"></i> Cache</a></li>

                    <li><a class="dropdown-item" href="./?do=reset-logs"
                           onclick="return confirm('Are you sure, you want to delete all logfiles?')"><i
                                    class="fa-regular fa-trash-can"></i> Logs</a></li>

                </ul>
                </div>
            </div>
        </div>

    </div>
    <script>
        const selectRow = (e) => {
            let selectedRow = $(e);
            let modelName = selectedRow.data('model');
            if (confirm('are you sure, you want to select this model "' + modelName + '"?')) {
                $('#modelTable tbody tr.table-info').removeAttr('class');
                selectedRow.addClass('table-info')
                $('#apiLanguageModel').val(modelName);
            }
        }
    </script>
<?php
$document->end()->send();