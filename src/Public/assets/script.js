document.addEventListener('DOMContentLoaded', function () {
    // Initialisation Swiper

    const autoplay = `<?php echo get_option('audio_carousel_options')['autoplay'] ? 'true' : 'false'; ?>`;
    const autoplayDelay = `<?php echo get_option('audio_carousel_options')['autoplay_delay'] ?? 1000000; ?>`;

    const swiper = new Swiper('.swiper-container', {
        loop: true,
        autoplay: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });

    // Gestion de la lecture audio
    const audioElements = {};
    document.querySelectorAll('.audio-item').forEach((item, index) => {
        const trackUrl = item.getAttribute('data-track');
        audioElements[index] = new Audio(trackUrl);

        item.querySelector('.play-button').addEventListener('click', () => {
            Object.values(audioElements).forEach(audio => audio.pause());
            const currentAudio = audioElements[index];
            if (currentAudio.paused) {
                currentAudio.play();
            } else {
                currentAudio.pause();
            }
        });
    });
});
