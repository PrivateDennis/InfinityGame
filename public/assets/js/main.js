"use strict";
const initialElements = [
        {term: 'Fire', icon: '🔥'},
        {term: 'Water', icon: '💧'},
        {term: 'Earth', icon: '🌎'},
        {term: 'Wind', icon: '💨'},
    ],
    btnClasses = {
        default: 'btn btn-sm btn-outline-secondary',
        selected: 'btn btn-sm btn-primary',
        new: 'btn btn-sm btn-success',
        brandNew: 'btn btn-sm btn-warning',
    };

gameInit = gameInit || [];
let termBucket = [], buttonContainer, loader, soundLib;

/**
 * .append('<audio controls autoplay style="display: none;"><source src="/bs/template/assets/media/Apex.mp3" type="audio/mpeg"></audio>')
 */
gameInit.push(() => {
    buttonContainer = $('.btn-container');

    const resetBuckets = () => {
            termBucket = [];
        },

        resetButtons = () => {
            $('.btn', buttonContainer).attr('class', btnClasses.default);
        },

        addButton = (term, icon, className) => {

            let found = initialElements.find((e, a) => {
                if (e.term === term) {
                    $('.btn:contains("' + e.term + '")', buttonContainer).attr('class', btnClasses.brandNew);
                    return true;
                }
            });

            if (found) {
                soundLib.denied.play();
                return
            }

            className = className || btnClasses.new;

            (btnClasses.new) ? soundLib.new.play() : soundLib.result.play();

            let newButton = $(`<button class="${className}" data-term="${term}">${term} ${icon}</button>`).on('click', buttonClickEvent)
            buttonContainer.append(newButton);
            initialElements.push({term, icon});
        },

        buttonClickEvent = (el) => {
            el.preventDefault();

            soundLib.click.play();
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
                    resetBuckets();
                    addButton(res.response, res.icon, className);
                    loader.hide();
                }).fail((res) => {
                    loader.hide();
                    $('#raw-response').val('ERROR LOOK IN CONSOLE.LOG!');
                    alert('ERROR LOOK IN CONSOLE.LOG!');
                });
            }
        },

        initGame = () => {
            loader = $('#loader');
            let l = 0;
            for (l in initialElements) {
                let item = initialElements[l];
                let newButton = $(`<button class="${btnClasses.default}" data-term="${item.term}">${item.term} ${item.icon}</button>`).on('click', buttonClickEvent)
                buttonContainer.append(newButton);
            }

            loader.hide();
            soundLib = {
                click: new Audio("https://cdn.freesound.org/previews/264/264446_4322723-lq.mp3"),
                result: new Audio("https://cdn.freesound.org/previews/264/264447_4322723-lq.mp3"),
                new: new Audio("https://cdn.freesound.org/previews/718/718435_12385710-lq.mp3"),
                denied: new Audio("https://cdn.freesound.org/previews/687/687451_14981990-lq.mp3"),
            }


//             const audio = new Audio("https://cdn.freesound.org/previews/264/264446_4322723-lq.mp3");
//             audio.play();
//             console.log('audio',audio)
//
// let audios = $('<audio controls autoplay><source src="/chatGameGPT/public/assets/media/Apex.mp3" type="audio/mpeg"></audio>');
//             $('body').append(audios);
//             console.log('audio',audios)
        };

    $('#quickPost').on('submit', (e) => {
        e.preventDefault();

        let prompt = $('#prompt-input').val();

        loader.show();
        $.ajax({
            method: 'POST',
            data: {prompt}
        }).success((res) => {
            console.log('res', res)
            $('#raw-response').val(res.response);
            loader.hide();
        }).fail((res) => {
            loader.hide();
            $('#raw-response').val('ERROR LOOK IN CONSOLE.LOG!');
            alert('ERROR LOOK IN CONSOLE.LOG!');
        });
    })

    initGame();
});