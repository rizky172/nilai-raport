<script>
export default {
    name: 'pop-up',
    props: {
        'image': {
            type: Array,
            required: true
        },
        'title': {
            type: String,
            required: false
        },
    },
    data: function() {
        return {
            data: {},
        };
    },
    mounted: function() {
        var vm = this;
        $('#popup').on('hidden.bs.modal', function (e) {
            vm.$store.dispatch('app/popup', false);
        });

        $('#popup-modal').carousel({
            interval: 5000,
            pause: false
        });

        $('#popup-modal').carousel('cycle');

        this.init();
    },
    watch: {
        'isShow': function() {
            this.init();
        }
    },
    computed: {
        // Get sidebar is shown or not
        isShow: function() {
            return this.$store.state.app.popupShow;
        }
    },
    methods: {
        init: function() {
            // Show hide depend on isShow
            if(this.isShow)
                $('#popup').modal('show');
            else
                $('#popup').modal('hide');
        },
        prev: function() {
            $('#popup-modal').carousel('prev');
        },
        next: function() {
            $('#popup-modal').carousel('next');
        }
    }
}
</script>

<template>
    <div>
        <div class="modal fade" id="popup">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="popup-modal" class="carousel slide">
                            <div class="carousel-inner">
                                <div v-for="(tr, $index) in image" :class="$index == 0 ? 'carousel-item active' : 'carousel-item'">
                                    <img class="d-block w-100" :src="tr" :alt="tr">
                                </div>
                            </div>
                            <a role="button" class="carousel-control-prev" @click="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a role="button" class="carousel-control-next" @click="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>