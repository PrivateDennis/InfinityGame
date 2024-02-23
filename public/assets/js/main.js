"use strict";
let localStoredList;

const initialElements = [
    {term: 'Fire', icon: '🔥'},
    {term: 'Water', icon: '💧'},
    {term: 'Earth', icon: '🌎'},
    {term: 'Air', icon: '💨'},
], btnClasses = {
    default: 'btn btn-sm btn-outline-secondary',
    selected: 'btn btn-sm btn-primary',
    new: 'btn btn-sm btn-success',
    brandNew: 'btn btn-sm btn-warning',
};

gameInit = gameInit || [];
let termBucket = [], buttonContainer, loader, soundLib;

const resetLocalStorage = () => {
        if (confirm('Are you sure, you want to delete all your crafted items?')) {
            localStorage.removeItem('elementList');
            window.location.reload(1);
        }
        return;
    },
    resetAll = () => {
        if (confirm('Are you sure, you want to delete all your crafted items?')) {
            localStorage.removeItem('elementList');
            window.location.href = './?do=reset-all';
        }
        return
    };
gameInit.push(() => {
    buttonContainer = $('.btn-container');

    const resetBuckets = () => {
            termBucket = [];
        },

        resetButtons = () => {
            $('.btn', buttonContainer).attr('class', btnClasses.default);
        },

        addButton = (term, icon, className) => {

            let found = localStoredList.find((e, a) => {
                if (e.term === term) {
                    soundLib.denied.play();
                    $('.btn[data-term="' + e.term + '"]', buttonContainer).attr('class', btnClasses.new);
                    $('.info-bar').text(`you found ${icon} ${term} again`);
                    return true;
                }
            });

            if (found) {
                return
            }

            className = className || btnClasses.new;

            if (className === btnClasses.brandNew) {
                soundLib.new.play();
                $('.info-bar').text(`Congratulation, you´ve have discovered ${icon} ${term}!`);
            } else {
                soundLib.result.play();
                $('.info-bar').text(`Nice, you´ve found ${icon} ${term}!`);
            }

            // let newButton = $(`<button class="${className}" data-term="${term}">${icon} ${term}</button>`).on('click', buttonClickEvent)
            let newButton = createButton({term, icon}, className);

            buttonContainer.append(newButton);
            localStoredList.push({term, icon});
            localStorage.setItem('elementList', JSON.stringify(localStoredList));
        },

        buttonClickEvent = (el) => {
            el.preventDefault();

            soundLib.click.play();
            let button = $(el.currentTarget);
            let term = button.data('term');

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

                    let traceLog = $('#traceLog');
                    if (traceLog.length) {
                        traceLog.val(res.trace.join("\n"));
                    }

                }).fail((res) => {
                    loader.hide();
                    $('#raw-response').val('ERROR LOOK IN CONSOLE.LOG!');
                    alert('ERROR LOOK IN CONSOLE.LOG!');
                });
            } else {
                resetButtons();
                button.attr('class', btnClasses.selected);
                $('.info-bar').text(`${term} selected. Select second element`);
            }
        },
        createButton = (item, className) => {
            className = className || btnClasses.default;
            return $(`<button class="${className}" data-term="${item.term}">${item.icon} ${item.term}</button>`).on('click', buttonClickEvent)
        },
        initGame = () => {
            loader = $('#loader');

            soundLib = {
                click: new Audio("https://cdn.freesound.org/previews/264/264446_4322723-lq.mp3"),
                result: new Audio("https://cdn.freesound.org/previews/264/264447_4322723-lq.mp3"),
                new: new Audio("https://cdn.freesound.org/previews/718/718435_12385710-lq.mp3"),
                denied: new Audio("https://cdn.freesound.org/previews/687/687451_14981990-lq.mp3"),
            }

            localStoredList = JSON.parse(localStorage.getItem('elementList'));

            if (!localStoredList) {
                localStorage.setItem('elementList', JSON.stringify(initialElements));
                localStoredList = JSON.parse(localStorage.getItem('elementList'));
            }

            let l = 0;
            for (l in localStoredList) {
                let item = localStoredList[l];
                let newButton = createButton(item, btnClasses.default)
                buttonContainer.append(newButton);
            }

            loader.hide();
            $('.info-bar').text('Select two items - can also be the same to craft and create infinite new elements.');
        };

    $('#quickPost').on('submit', (e) => {
        e.preventDefault();

        let prompt = $('#prompt-input').val();
        $('#raw-response').append(`you: ${prompt}<br/><br/>`);

        loader.show();

        $.ajax({
            method: 'POST',
            data: {do: 'prompt', prompt}
        }).success((res) => {
            $('#raw-response').append(`model: ${res.response}<br/><br/>`);
            loader.hide();
        }).fail((res) => {
            loader.hide();
            $('#raw-response').val('ERROR LOOK IN CONSOLE.LOG!');
            alert('ERROR LOOK IN CONSOLE.LOG!');
        });

    })

    initGame();
});