<template>
    <modal name="errors-modal">
        <div class="card modal-card">
            <div class="card-header">
                <h4 class="text-center text-danger">ERROR</h4>
            </div>
            <div class="card-body">
                <p>{{ error }}</p>
            </div>
            <div class="card-footer text-center">
                <button class="btn btn-default" @click="$modal.hide('errors-modal')">OK</button>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {
        data() {
            return {
                error: '',
            }
        },
        methods: {
            getErrors(event) {
                this.error = event.message || event;
                this.$modal.show('errors-modal');
            },
        },
        created() {
            this.$eventBus.$on('errorMessage', this.getErrors);
        },
        beforeDestroy() {
            this.$eventBus.$off('errorMessage');
        },
    }
</script>

<style lang="css" scoped>

</style>
