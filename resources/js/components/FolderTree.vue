<template>
    <div class="folder-tree">
        <span class="badge badge-secondary title">{{folder.name}}</span>
        <span v-if="move && !onlyView">
            <span class="badge badge-warning action" @click="$eventBus.$emit('moveHere', folder.id)">move here</span>
        </span>
        <span class="actions">
            <span class="badge badge-default action">actions</span>
            <span v-if="!move && !onlyView" class="badge badge-info action" @click="openAddFolder(folder)">add folder</span>
            <span v-if="!move && folder.name !== 'root' && !onlyView">
                <span class="badge badge-warning action" @click="openEditFolder(folder)">rename</span>
                <span class="badge badge-info action" @click="openAccessFolder(folder)">access</span>
                <span class="badge badge-danger action" @click="openDeleteFolder(folder)">delete</span>
            </span>
        </span>
        <div>
            <div v-if="!move">
                <div v-for="file in folder.files" :key="file.id">
                    <span class="badge badge-light">{{file.name}}</span>
                    <span v-if="!onlyView" class="actions">
                        <span class="badge badge-default action">actions</span>
                        <span class="badge badge-warning action" @click="$eventBus.$emit('moveFile', file)">move</span>
                        <span class="badge badge-success action" @click="openEditFile(file)">rename</span>
                        <span class="badge badge-info action" @click="openAccessFile(file)">access</span>
                        <span class="badge badge-danger action" @click="openDeletingFile(file)">delete</span>
                    </span>
                </div>
            </div>
            <div v-for="subFolder in folder.folders" :key="subFolder.id">
                <folder-tree :folder="subFolder" :move="move" :only-view="onlyView"/>
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
            onlyView: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            openAddFolder(parentFolder) {
                const newFolder = {
                    name: '',
                    parent_folder_id: parentFolder.id
                }
                this.$eventBus.$emit('addFolderModal', newFolder);
            },
            openEditFile(file) {
                this.$eventBus.$emit('renameFileModal', file);
            },
            openEditFolder(folder) {
                this.$eventBus.$emit('renameFolderModal', folder);
            },
            openDeletingFile(file) {
                this.$eventBus.$emit('deleteFileModal', file);
            },
            openDeleteFolder(folder) {
                this.$eventBus.$emit('deleteFolderModal', folder);
            },
            openAccessFile(file) {
                this.$eventBus.$emit('accessFileModal', file);
            },
            openAccessFolder(folder) {
                this.$eventBus.$emit('accessFolderModal', folder);
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
.actions .action {
    display: none;
}
.actions .badge-default {
    display:inline-block;
}
.actions:hover .action {
    display:inline-block;
}
.actions:hover .badge-default {
    display:none;
}
</style>
