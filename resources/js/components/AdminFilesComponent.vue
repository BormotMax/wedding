<template>
    <div class="row">
        <div class="col-md-4">
            <h2>Upload file</h2>
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" @submit.prevent="handleSubmit">
                        <input type="file" class="form-control" ref="file" @change="handleFileUpload">
                        <button class="btn btn-success" type="submit">send</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <files-tree />
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                file: '',
                csrf: '',
            }
        },
        methods: {
            handleFileUpload() {
                this.file = this.$refs.file.files[0];
            },
            handleSubmit() {
                let formData = new FormData();
                formData.append('file', this.file);
                formData.append('_token', this.csrf);
                const url = process.env.MIX_API_URL + '/admin/files';
                axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then((res) => {
                    this.$refs.file.value = '';
                    this.file = '';
                    this.$emit('newFile');
                }).catch((err) => {
                    console.log('error', err);
                });
            },
        },
        mounted() {
            this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }
</script>
