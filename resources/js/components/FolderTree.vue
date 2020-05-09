<template>
    <div class="folder-tree">
        <span class="badge badge-secondary title">{{folder.name}}</span>
        <span v-if="move">
            <span class="b  adge badge-warning action" @click="$eventBus.$emit('moveHere', folder.id)">move here</span>
        </span>
        <span v-if="!move" class="badge badge-info action" @click="openAddFolder()">add folder</span>
        <span v-if="!move && folder.name !== 'root'">
            <span class="badge badge-warning action" @click="openEditFolder(folder)">rename</span>
            <span class="badge badge-info action" @click="openAccessFolder(folder)">access</span>
            <span class="badge badge-danger action" @click="openDeleteFolder(folder)">delete</span>
        </span>
        <div v-if="isAddingFolder" class="form-inline">
            <label>new folder name</label>
            <input type="text" v-model="newFolderName" class="form-cotrol"/>
            <button class="btn btn-success btn-sm" @click="handleAddFolder()">add</button>
            <button class="btn btn-warning btn-sm" @click="closeAllModals()">cancel</button>
        </div>
        <div v-if="isRanamingFolder" class="form-inline">
            <label>edit folder name</label>
            <input type="text" v-model="editingFolder.name" class="form-cotrol"/>
            <button class="btn btn-success btn-sm" @click="handleEditFolder()">update</button>
            <button class="btn btn-warning btn-sm" @click="closeAllModals()">cancel</button>
        </div>
        <div v-if="isDeletingFolder" class="form-inline">
            <label>confirm folder delete</label>
            <button class="btn btn-danger btn-sm" @click="handleDeleteFolder()">delete</button>
            <button class="btn btn-warning btn-sm" @click="closeAllModals()">cancel</button>
        </div>
        <div v-if="isAccesFolderOpen" class="form-inline">
            <label>hide access for</label>
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
            <button class="btn btn-success btn-sm" @click="updateFolderAccess()">update</button>
            <button class="btn btn-warning btn-sm" @click="closeAllModals()">cancel</button>
        </div>
        <div>
            <div v-if="!move">
                <div v-for="file in folder.files" :key="file.id">
                    <span class="badge badge-light">{{file.name}}</span>
                    <span class="badge badge-warning action" @click="$eventBus.$emit('moveFile', file)">move</span>
                    <span class="badge badge-success action" @click="openEditFile(file)">rename</span>
                    <span class="badge badge-info action" @click="openAccessFile(file)">access</span>
                    <span class="badge badge-danger action" @click="openDeletingFile(file)">delete</span>
                </div>
                <div v-if="isRanamingFile" class="form-inline">
                    <label>edit file name</label>
                    <input type="text" v-model="editingFile.name" class="form-cotrol"/>
                    <button class="btn btn-success btn-sm" @click="handleEditFile()">update</button>
                    <button class="btn btn-warning btn-sm" @click="closeAllModals()">cancel</button>
                </div>
                <div v-if="isDeletingFile" class="form-inline">
                    <label>confirm file delete</label>
                    <button class="btn btn-danger btn-sm" @click="handleDeleteFile()">delete</button>
                    <button class="btn btn-warning btn-sm" @click="closeAllModals()">cancel</button>
                </div>
                <div v-if="isAccesFileOpen" class="form-inline">
                    <label>hide access for</label>
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
                    <button class="btn btn-success btn-sm" @click="updateFileAccess()">update</button>
                    <button class="btn btn-warning btn-sm" @click="closeAllModals()">cancel</button>
                </div>
            </div>
            <div v-for="subFolder in folder.folders" :key="subFolder.id">
                <folder-tree :folder="subFolder" :move="move" :roles="roles"/>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        props: {
            folder: Object,
            move: {
                type: Boolean,
                default: false
            },
            roles: Array
        },
        data() {
            return {
                isAddingFolder: false,
                isRanamingFile: false,
                newFolderName: '',
                editingFile: null,
                isRanamingFolder: false,
                editingFolder: null,
                isDeletingFile: false,
                isDeletingFolder: false,
                isAccesFileOpen: false,
                isAccesFolderOpen: false,
            }
        },
        methods: {
            openAddFolder() {
                this.closeAllModals();
                this.isAddingFolder = true;
            },
            handleAddFolder() {
                this.$eventBus.$emit('createFolder', {
                    name: this.newFolderName,
                    parentFolderId: this.folder.id
                });
                this.closeAllModals();
            },
            openEditFile(file) {
                this.closeAllModals();
                this.isRanamingFile = true;
                this.editingFile = {...file};
            },
            handleEditFile() {
                this.$eventBus.$emit('editFile', this.editingFile);
                this.closeAllModals();
            },
            openEditFolder(folder) {
                this.closeAllModals();
                this.isRanamingFolder = true;
                this.editingFolder = {...folder};
            },
            handleEditFolder() {
                this.$eventBus.$emit('editFolder', this.editingFolder);
                this.closeAllModals();
            },
            openDeletingFile(file) {
                this.closeAllModals();
                this.editingFile = {...file};
                this.isDeletingFile = true;
            },
            closeAllModals() {
                this.isAddingFolder = false;
                this.isRanamingFile = false;
                this.newFolderName = '';
                this.editingFile = null;
                this.isRanamingFolder = false;
                this.editingFolder = null;
                this.isDeletingFile = false;
                this.isDeletingFolder = false;
                this.isAccesFileOpen = false;
                this.isAccesFolderOpen = false;
            },
            handleDeleteFile() {
                this.$eventBus.$emit('deleteFile', this.editingFile);
                this.closeAllModals();
            },
            openDeleteFolder(folder) {
                this.closeAllModals();
                this.editingFolder = {...folder};
                this.isDeletingFolder = true;
            },
            handleDeleteFolder() {
                this.$eventBus.$emit('deleteFolder', this.editingFolder);
                this.closeAllModals();
            },
            openAccessFile(file) {
                this.editingFile = {...file};
                this.isAccesFileOpen = true;
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
            updateFileAccess() {
                this.$eventBus.$emit('updateFileAccess', this.editingFile);
                this.closeAllModals();
            },
            openAccessFolder(folder) {
                this.editingFolder = {...folder};
                this.isAccesFolderOpen = true;
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
            updateFolderAccess() {
                this.$eventBus.$emit('updateFolderAccess', this.editingFolder);
                this.closeAllModals();
            },
        }
    }
</script>

<style lang="css" scoped>
.folder-tree {
    padding-left: 15px;
}
.action {
    cursor: pointer;
}
.role {
    margin: 0 5px;
}
</style>
