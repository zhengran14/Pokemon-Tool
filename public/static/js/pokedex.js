const imgUrl = '/static/res/pm_img/';
var currentPage = 0, totalPages = 1;

$(document).ready(function () {
    getPokemons();
});

function autoNextPage() {
    let needNextPage = $(document).height() - $(window).height() - $(window).scrollTop() < $('.pm:nth-child(1)').height();
    if (needNextPage) {
        getPokemons();
    }
};

function getPokemons() {
    if (currentPage + 1 > totalPages) {
        return;
    }
    currentPage++;
    if ($('#initPageLoading').length <= 0) {
        $('#pageLoading').removeClass('d-none');
    }
    $(window).unbind('resize', autoNextPage);
    $(window).unbind('scroll', autoNextPage);
    $.ajax({
        type: 'GET',
        url: `/pokedex/list?page=${currentPage}`,
        contentType: 'application/json;charset=UTF-8',
        async: true,
        success: function (data) {
            result = JSON.parse(data);
            console.log(result);
            totalPages = result.totalPages;
            for (const pokemon of result.pokemons) {
                let div = $(`<div id="pm${pokemon.id}" class="pm col col-6 col-sm-4 col-md-3 col-xxl-2">
                                <div class="card h-100">
                                <picture>
                                    <source media="(min-width: 992px)" srcset="${imgUrl}315/${pokemon.img_file}">
                                    <source media="(min-width: 576px)" srcset="${imgUrl}210/${pokemon.img_file}">
                                    <source srcset="${imgUrl}210/${pokemon.img_file}">
                                    <img decoding="async" src="${imgUrl}315/${pokemon.img_file}" class="card-img-top" alt="${pokemon.name_zh_cn}">
                                </picture>
                                <div class="card-body border-top">
                                    <div class="row text-secondary fs-5 ms-1">${pokemon.zukan_id.toString().padStart(4, '0')}</div>
                                    <div class="row fs-4 ms-1">${pokemon.name_zh_cn}</div>
                                </div>
                                </div>
                            </div>`);
                let pokemonSpecialForms = pokemon.pokemonSpecialForms.map(pmsf => pmsf.name_zh_cn).join(' / ');
                div.find('.card-body').append(`<div class="row text-secondary fs-6 ms-1">${pokemonSpecialForms}</div>`);
                $('#pokemonPanel').append(div);
                let pokemonTypes = $('<div class="card-footer border-0 bg-transparent row row-cols-auto column-gap-1 ms-1"></div>');
                for (const pokemonType of pokemon.pokemonTypes) {
                    pokemonTypes.append(`
                        <span class="badge rounded-pill fs-5 fw-normal" style="background-color: ${pokemonType.color}">
                            <span class="m-0 m-lg-3">${pokemonType.name_zh_cn}</span>
                        </span>`);
                }
                div.find('.card-body').after(pokemonTypes);
            }
        },
        complete: function () {
            if ($('#initPageLoading')) {
                $('#initPageLoading').remove();
            }
            $('#pageLoading').addClass('d-none');
            $(window).bind('resize', autoNextPage);
            $(window).bind('scroll', autoNextPage);
        }
    });
}