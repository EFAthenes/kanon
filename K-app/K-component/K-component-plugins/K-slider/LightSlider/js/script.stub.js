const sliderUpdated = () => {
    const selected = $('#my-slider-id .lslide.active');

    $('#my-slider-id .name').html(selected.attr('data-name'));
    $('#my-slider-id .reference').html(selected.attr('data-ref'));
    $('#my-slider-id .num').html(selected.attr('data-num'));

    $('#my-slider-id .slider-num').val(parseInt(selected.attr('data-num')) - 1)
}

$('#my-slider-id .info').on('click', sliderUpdated)

let lightSlider = null;



/**
 * J'ai observe que si il y'a plusieurs images que le slider beug.
 * Pour cela je propose comme solution de faire un Lazy-loading, 
 * ce qui veut dire que les images seronts chargees en batches (chunks),
 * Le code en bas OnSliderLoad je l'ai copie de stackoverflow, juste pour une demonstration.
 * Le lazy-loading peut me prendre un peu de temps parce que je dois aller sur le code de ligthSlider et le debeuger. 
 * 
 */
setTimeout(() => {
    lightSlider = $('#my-slider-id .lightSlider').lightSlider({
        sliderHeight: '400px',

        gallery: true,
        item: '#items-number',
        loop: true,
        slideMargin: 0,
        thumbItem: '#thumbs-number',
        onSliderLoad: sliderUpdated,
        onAfterSlide: sliderUpdated
    })
}, 100);

$('#my-slider-id .slider-num').on('change', () => {
    const sliderNum = $('#my-slider-id .slider-num').val();

    lightSlider.goToSlide(parseInt(sliderNum) + 1)
})