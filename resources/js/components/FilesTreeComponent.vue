<template>
    <div class="container">
        <h2>Files</h2>
        <div>
            <folder-tree :folder="tree.root" :move="moveMode" :roles="roles"/>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                files: [],
                folders: [],
                tree: {root:{}},
                moveMode: false,
                movingFile: null,
                roles: [],
            }
        },
        computed: {
            apiHeaders: () => {
                return {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        },
        created() {
            this.$parent.$on('newFile', this.fetchFolders);
            this.$eventBus.$on('moveFile', this.switchMoveMode);
            this.$eventBus.$on('createFolder', this.createFolder);
            this.$eventBus.$on('moveHere', this.handleMove);
            this.$eventBus.$on('editFile', this.handleEditFile);
            this.$eventBus.$on('editFolder', this.handleEditFolder);
            this.$eventBus.$on('deleteFile', this.handleDeleteFile);
            this.$eventBus.$on('deleteFolder', this.handleDeleteFolder);
            this.$eventBus.$on('updateFileAccess', this.handleUpdateFileAccess);
            this.$eventBus.$on('updateFolderAccess', this.handleUpdateFolderAccess);
        },
        mounted() {
            this.fetchFolders();
            this.fetchRoles();
        },
        beforeDestroy() {
            this.$parent.$off('newFile');
            this.$eventBus.$off('moveFile');
            this.$eventBus.$off('createFolder');
            this.$eventBus.$off('editFile');
            this.$eventBus.$off('editFolder');
            this.$eventBus.$off('deleteFile');
            this.$eventBus.$off('deleteFolder');
            this.$eventBus.$off('updateFileAccess');
            this.$eventBus.$off('updateFolderAccess');
        },
        methods: {
            fetchFiles() {
                const url = process.env.MIX_API_URL + '/files/get';
                axios.get(url, this.apiHeaders).then((res) => {
                    this.files = res.data.data.files;
                    this.buildTree();
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            fetchFolders() {
                const url = process.env.MIX_API_URL + '/folders/get';
                axios.get(url, this.apiHeaders).then((res) => {
                    this.folders = res.data.data.folders;
                    this.fetchFiles();
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            buildTree() {
                let tree = {root:{}};
                this.folders.forEach(folder => {
                    if (folder.name === 'root') {
                        tree.root = folder;
                    }
                });
                tree.root.folders = this.getSubFolders(tree.root.id);
                tree.root.files = this.getFolderFiles(tree.root.id);
                this.tree = tree;
            },
            getSubFolders(parentId) {
                let folders = [];
                this.folders.forEach(folder => {
                    if (folder.parent_folder_id === parentId) {
                        folder.folders = this.getSubFolders(folder.id);
                        folder.files = this.getFolderFiles(folder.id);
                        folders.push(folder);
                    }
                });
                return folders;
            },
            getFolderFiles(folderId) {
                let files = [];
                this.files.forEach(file => {
                    if (file.folder_id === folderId) {
                        files.push(file);
                    }
                });
                return files;
            },
            switchMoveMode(file) {
                this.moveMode = true;
                this.movingFile = file;
            },
            createFolder({name, parentFolderId}) {
                this.moveMode = false;
                const url = process.env.MIX_API_URL + '/admin/folders';
                const data = {
                    name,
                    parent_folder_id: parentFolderId
                };
                axios.post(url, data, this.apiHeaders).then((res) => {
                    if (res.data.data.success) {
                        this.folders.push(res.data.data.folder);
                        this.fetchFolders();
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            updateFile(file) {
                const url = process.env.MIX_API_URL + '/admin/files/' + file.id;
                const data = {
                    folder_id: file.folder_id,
                    name: file.name
                };
                axios.put(url, data, this.apiHeaders).then((res) => {
                    if (res.data.data.file) {
                        this.fetchFolders();
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            handleMove(folderId) {
                const file = {
                    id: this.movingFile.id,
                    folder_id: folderId,
                    name: this.movingFile.name
                };
                this.movingFile = null;
                this.moveMode = false;
                this.updateFile(file);
            },
            handleEditFile(file) {
                this.updateFile(file);
            },
            handleEditFolder(folder) {
                const url = process.env.MIX_API_URL + '/admin/folders/' + folder.id;
                const data = {
                    name: folder.name
                };
                axios.put(url, data, this.apiHeaders).then((res) => {
                    if (res.data.data.folder) {
                        this.fetchFolders();
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            handleDeleteFile(file) {
                const url = process.env.MIX_API_URL + '/admin/files/' + file.id;
                axios.delete(url, this.apiHeaders).then((res) => {
                    if (res.data.data.success) {
                        this.fetchFolders();
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            handleDeleteFolder(folder) {
                const url = process.env.MIX_API_URL + '/admin/folders/' + folder.id;
                axios.delete(url, this.apiHeaders).then((res) => {
                    if (res.data.data.success) {
                        this.fetchFolders();
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            fetchRoles() {
                const url = process.env.MIX_API_URL + '/roles';
                axios.get(url, this.apiHeaders).then((res) => {
                    if (res.data.data.roles) {
                        this.roles = res.data.data.roles.filter(role => role.role_name !== 'admin');
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            handleUpdateFileAccess(file) {
                const url = process.env.MIX_API_URL + '/admin/files/access/' + file.id;
                const data = {
                    roles: file.roles.map(role => role.id)
                };
                axios.put(url, data, this.apiHeaders).then((res) => {
                    if (res.data.data.success) {
                        this.fetchFolders();
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            },
            handleUpdateFolderAccess(folder) {
                const url = process.env.MIX_API_URL + '/admin/folders/access/' + folder.id;
                const data = {
                    roles: folder.roles.map(role => role.id)
                };
                axios.put(url, data, this.apiHeaders).then((res) => {
                    if (res.data.data.success) {
                        this.fetchFolders();
                    }
                }).catch((err) => {
                    console.log('ERRORS', err);
                });
            }
        }
    }
</script>
