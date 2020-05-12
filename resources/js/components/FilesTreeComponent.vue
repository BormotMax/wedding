<template>
    <div class="container">
        <h2>Files</h2>
        <div>
            <folder-tree :folder="tree.root" :move="moveMode" :only-view="onlyView"/>
        </div>
        <modal name="edit-file">
            <div class="card modal-card" v-if="editingFile">
                <div class="card-header">
                    Rename file <b>{{editingFile.name}}</b>
                </div>
                <div class="card-body">
                    <input class="form-control" v-model="editingFile.name">
                </div>
                <div class="card-footer">
                    <button class="btn btn-success pull-left" @click="handleEditFile()">Rename</button>
                    <button class="btn btn-default pull-right" @click="$modal.hide('edit-file')">Cancel</button>
                </div>
            </div>
        </modal>
        <modal name="file-access">
            <div class="card modal-card" v-if="editingFile">
                <div class="card-header">
                    Hide access to file <b>{{editingFile.name}}</b> to roles:
                </div>
                <div class="card-body">
                    <span
                        v-for="fileRole in editingFile.roles"
                        :key="fileRole.id"
                        class="badge badge-warning action role"
                        @click="removeFileRole(fileRole.id)"
                    >
                        {{fileRole.role_name}} x
                    </span>
                    <select class="form-control" @change="addAccessFile">
                        <option key="0" value="0">role</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{role.role_name}}
                        </option>
                    </select>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success pull-left" @click="handleUpdateFileAccess()">Update</button>
                    <button class="btn btn-default pull-right" @click="$modal.hide('file-access')">Cancel</button>
                </div>
            </div>
        </modal>
        <modal name="edit-folder">
            <div class="card modal-card" v-if="editingFolder">
                <div class="card-header">
                    Rename folder <b>{{editingFolder.name}}</b>
                </div>
                <div class="card-body">
                    <input class="form-control" v-model="editingFolder.name">
                </div>
                <div class="card-footer">
                    <button class="btn btn-success pull-left" @click="handleRanameFolder()">Rename</button>
                    <button class="btn btn-default pull-right" @click="$modal.hide('edit-folder')">Cancel</button>
                </div>
            </div>
        </modal>
        <modal name="folder-access">
            <div class="card modal-card" v-if="editingFolder">
                <div class="card-header">
                    Hide access to folder <b>{{editingFolder.name}}</b> to roles:
                </div>
                <div class="card-body">
                    <span
                        v-for="folderRole in editingFolder.roles"
                        :key="folderRole.id"
                        class="badge badge-warning action role"
                        @click="removeFolderRole(folderRole.id)"
                    >
                        {{folderRole.role_name}} x
                    </span>
                    <select class="form-control" @change="addAccessFolder">
                        <option key="0" value="0">role</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{role.role_name}}
                        </option>
                    </select>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success pull-left" @click="handleUpdateFolderAccess()">Update</button>
                    <button class="btn btn-default pull-right" @click="$modal.hide('folder-access')">Cancel</button>
                </div>
            </div>
        </modal>
        <modal name="add-folder">
            <div class="card modal-card" v-if="editingFolder">
                <div class="card-header">
                    Add folder
                </div>
                <div class="card-body">
                    <input class="form-control" v-model="editingFolder.name">
                </div>
                <div class="card-footer">
                    <button class="btn btn-success pull-left" @click="handleAddFolder()">Create</button>
                    <button class="btn btn-default pull-right" @click="$modal.hide('add-folder')">Cancel</button>
                </div>
            </div>
        </modal>
        <v-dialog />
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
                roles: [],
                editingFile: null,
                editingFolder: null,
            }
        },
        props: {
            onlyView: {
                type: Boolean,
                default: false
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
            this.$eventBus.$on('moveHere', this.handleMove);
            this.$eventBus.$on('deleteFileModal', this.handleDeleteFileModal);
            this.$eventBus.$on('renameFileModal', this.handleRenameModal);
            this.$eventBus.$on('accessFileModal', this.handleAccessFileModal);
            this.$eventBus.$on('deleteFolderModal', this.handleDeleteFolderModal);
            this.$eventBus.$on('renameFolderModal', this.handleRenameFolderModal);
            this.$eventBus.$on('accessFolderModal', this.handleAccessFolderModal);
            this.$eventBus.$on('addFolderModal', this.handleAddFolderModal);
        },
        mounted() {
            this.fetchFolders();
            this.fetchRoles();
        },
        beforeDestroy() {
            this.$parent.$off('newFile');
            this.$eventBus.$off('moveFile');
            this.$eventBus.$off('moveHere');
            this.$eventBus.$off('deleteFileModal');
            this.$eventBus.$off('renameFileModal');
            this.$eventBus.$off('accessFileModal');
            this.$eventBus.$off('deleteFolderModal');
            this.$eventBus.$off('renameFolderModal');
            this.$eventBus.$off('accessFolderModal');
            this.$eventBus.$off('addFolderModal');
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
                this.editingFile = file;
            },
            createFolder(folder) {
                this.moveMode = false;
                const url = process.env.MIX_API_URL + '/admin/folders';
                const data = {...folder};
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
                    id: this.editingFile.id,
                    folder_id: folderId,
                    name: this.editingFile.name
                };
                this.editingFile = null;
                this.moveMode = false;
                this.updateFile(file);
            },
            handleEditFile() {
                this.$modal.hide('edit-file');
                this.updateFile(this.editingFile);
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
            handleUpdateFileAccess() {
                this.$modal.hide('file-access');
                const file = {...this.editingFile};
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
            updateFolderAccess(folder) {
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
            },
            handleDeleteFileModal(file) {
                this.$modal.show('dialog', {
                    title: 'Delete file',
                    text: `Confirm delete file: ${file.name}`,
                    buttons: [
                        {
                            title: 'Delete',
                            handler: () => {
                                this.handleDeleteFile(file);
                                this.$modal.hide('dialog');
                            }
                        },
                        {
                            title: 'cancel'
                        }
                    ]
                });
            },
            handleRenameModal(file) {
                this.editingFile = {...file};
                this.$modal.show('edit-file');
            },
            addAccessFile(event) {
                const roleId = event.currentTarget.value;
                if (!roleId || roleId === '0') {
                    return;
                }
                const existingRole = this.editingFile.roles.find(item => item.id == roleId);
                if (existingRole) {
                    return;
                }
                const role = this.roles.find(item => item.id == roleId);
                this.editingFile.roles.push(role);
            },
            removeFileRole(roleId) {
                this.editingFile.roles = this.editingFile.roles.filter(role => role.id != roleId);
            },
            handleAccessFileModal(file) {
                this.editingFile = {...file};
                this.$modal.show('file-access');
            },
            handleDeleteFolderModal(folder) {
                this.$modal.show('dialog', {
                    title: 'Delete folder',
                    text: `Confirm delete folder: ${folder.name}`,
                    buttons: [
                        {
                            title: 'Delete',
                            handler: () => {
                                this.handleDeleteFolder(folder);
                                this.$modal.hide('dialog');
                            }
                        },
                        {
                            title: 'cancel'
                        }
                    ]
                });
            },
            handleRenameFolderModal(folder) {
                this.editingFolder = {...folder};
                this.$modal.show('edit-folder');
            },
            addAccessFolder(event) {
                const roleId = event.currentTarget.value;
                if (!roleId || roleId === '0') {
                    return;
                }
                const existingRole = this.editingFolder.roles.find(item => item.id == roleId);
                if (existingRole) {
                    return;
                }
                const role = this.roles.find(item => item.id == roleId);
                this.editingFolder.roles.push(role);
            },
            removeFolderRole(roleId) {
                this.editingFolder.roles = this.editingFolder.roles.filter(role => role.id != roleId);
            },
            handleAccessFolderModal(folder) {
                this.editingFolder = {...folder};
                this.$modal.show('folder-access');
            },
            handleAddFolderModal(folder) {
                this.editingFolder = {...folder};
                this.$modal.show('add-folder');
            },
            handleAddFolder() {
                this.$modal.hide('add-folder');
                this.createFolder(this.editingFolder);
            },
            handleRanameFolder() {
                this.handleEditFolder(this.editingFolder);
                this.$modal.hide('edit-folder');
            },
            handleUpdateFolderAccess() {
                this.updateFolderAccess(this.editingFolder);
                this.$modal.hide('folder-access');
            }
        }
    }
</script>
<style scoped>
.card.modal-card {
    height: 100%;
}
</style>