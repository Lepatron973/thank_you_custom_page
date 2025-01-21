<div class="swiper-container">
    
    <div class="swiper-wrapper">
        <?php if ( ! empty( $audios ) ): ?>
            <?php foreach ( $audios as $file ): ?>
                <div class="swiper-slide">
                    <div class="audio-item" data-track="<?php echo esc_url( $file['file_url'] ); ?>">
                        <button class="play-button">▶</button>
                        <p>Audio <?php echo esc_html( $file['title'] ); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?php esc_html_e( 'No audio files available.', 'audio-carousel' ); ?></p>
        <?php endif; ?>
    </div> <!-- swiper-wrapper -->
    
    <!-- Navigation et pagination -->
    <div class="swiper-pagination swiper-nav"></div>
    <div class="swiper-button-next swiper-nav"></div>
    <div class="swiper-button-prev swiper-nav"></div>
</div> <!-- swiper-container -->

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        // Initialiser Swiper
        var swiper = new Swiper(".swiper-container", {
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            }
        });

        // Gestion des boutons play/pause
        const playButtons = document.querySelectorAll(".play-button");
        playButtons.forEach(button => {
            button.addEventListener("click", function() {
                var audioItem = this.closest(".audio-item");
                var audioUrl = audioItem.getAttribute("data-track");

                // Créer un nouvel élément audio si ce n'est pas déjà un
                var audio = audioItem.querySelector("audio");
                if (!audio) {
                    audio = document.createElement("audio");
                    audio.setAttribute("src", audioUrl);
                    audio.setAttribute("controls", "true");
                    audioItem.appendChild(audio);
                }

                // Si la piste est déjà en cours de lecture, on la met en pause, sinon on la joue
                if (audio.paused) {
                    audio.play();
                    this.innerText = "❚❚"; // Change le bouton en pause
                } else {
                    audio.pause();
                    this.innerText = "▶"; // Change le bouton en play
                }
            });
        });
    });
</script>
