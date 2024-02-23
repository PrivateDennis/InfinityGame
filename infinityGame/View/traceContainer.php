<?php
?>
<style>
    .debug-container-fluid {
        position: fixed;
        bottom: 0;
        right: 0;
        margin: 15px;
        max-width: 1200px;
    }

    .debug-container-fluid .card {
        min-width: 460px;
        min-height: 200px;
    }
</style>
<div class="debug-container-fluid" style="z-index: 99999;">
    <div class="collapse" id="collapseDebugContainer">
        <div class="card card-body">
            <textarea class="form-control" id="traceLog"></textarea>
            <hr>
            <a class="text-decoration-none" data-bs-toggle="collapse" href="#collapseDebugContainer"
               aria-expanded="false"
               aria-controls="collapseDebugContainer">close</a>
        </div>
    </div>
</div>
<div class="debug-container-fluid" style="z-index: 99999;">
    <a data-bs-toggle="collapse" href="#collapseDebugContainer" aria-expanded="false"
       aria-controls="collapseDebugContainer">
        <i class="fa-solid fa-bug"></i></a>
</div>