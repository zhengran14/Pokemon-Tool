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
            for (const pokemon of result.pokemons) {
                let div = $(`<div class="col col-6 col-sm-3 col-md-2 col-xxl-1">
                                <div class="card h-100">
                                <img src="/static/res/pm_img/${pokemon.img_file}" class="card-img-top" alt="${pokemon.name_zh_cn}">
                                <div class="card-body border-top">
                                    <h5 class="card-title">${pokemon.name_zh_cn}</h5>
                                </div>
                                </div>
                            </div>`);
                for (const pokemonTypes of pokemon.pokemonTypes) {
                    div.find('.card-body').append(`<span class="badge rounded-pill" style="background-color: ${pokemonTypes.color}">${pokemonTypes.name_zh_cn}</span>`);
                }
                $('#pokemonPanel').append(div);
            }
        },
        complete: function () {}
    });
}