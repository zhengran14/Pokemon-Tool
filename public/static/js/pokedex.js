$(document).ready(function () {
    getPokemons(1);
});

function getPokemons(page) {
    $.ajax({
        type: 'GET',
        url: `/pokedex/list?page=${page}`,
        contentType: 'application/json;charset=UTF-8',
        async: true,
        success: function (data) {
            result = JSON.parse(data);
            console.log(result);
        },
        complete: function () {}
    });
}