<template>
    <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">

                <form>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="">
                            <div class="mb-4">
                                <label for="formBookTitle"
                                       class="block text-gray-700 text-sm font-bold mb-2">Title: </label>
                                <input type="text"
                                       v-model="form.title"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                       id="formBookTitle" placeholder="Enter Title">
                                <div v-if="$page.props.errors.title" class="text-red-500">{{ $page.props.errors.title }}</div>

                            </div>
                            <div class="mb-4">
                                <label for="formBookAuthor"
                                       class="block text-gray-700 text-sm font-bold mb-2">Author:</label>
                                <input type="text"
                                       v-model="form.author"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                       id="formBookAuthor" placeholder="Enter Author">
                                <div v-if="$page.props.errors.author" class="text-red-500">{{ $page.props.errors.author }}</div>

                            </div>
                            <div class="mb-4">
                                <label for="formBookImage"
                                       class="block text-gray-700 text-sm font-bold mb-2">Image:</label>
                                <file-pond
                                    name="imageFilepond"
                                    ref="pond"
                                    v-bind:allow-multiple="true"
                                    accepted-file-types="image/png, image/jpeg"
                                    v-bind:server="{
                                        url: '',
                                        timeout: 7000,
                                        process:{
                                            url: '/upload-books',
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': $page.props.csrf_token
                                            },
                                            withCredentials: false,
                                            onload: handleFilePondLoad,
                                            onerror: () => {}
                                        },
                                        remove: handleFilePondRemove,
                                        revert: handleFilePondRevert
                                    }"
                                    v-bind:files="myFiles"
                                    v-on:init="handleFilePondInit"
                                >
                                </file-pond>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button  type="button"
                                   v-show="!isEdit"
                                   @click="$emit('formsave', form)"
                                   class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                          >
                            Save
                          </button>
                        </span>
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                          <button  type="button"
                                   v-show="isEdit"
                                   @click="$emit('formsave', form)"
                                   class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                          >
                            Update
                          </button>
                        </span>
                        <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                          <button
                              type="button"
                              @click="$emit('formclose')"
                              class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Cancel
                          </button>
                        </span>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script>

    import axios from "axios";
    import vueFilePond from "vue-filepond";
    import "filepond/dist/filepond.min.css";

    // Import FilePond plugins
    // Please note that you need to install these plugins separately

    // Import image preview plugin styles
    import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

    // Import the plugin code
    import FilePondPluginFilePoster from 'filepond-plugin-file-poster';

    // Import the plugin styles
    import 'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css';

    // Import image preview and file type validation plugins
    import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
    import FilePondPluginImagePreview from "filepond-plugin-image-preview";

    const FilePond = vueFilePond(
        FilePondPluginFileValidateType,
        FilePondPluginImagePreview,
        FilePondPluginFilePoster
    );

    export default {
        props: ['form', 'isOpen', 'isEdit'],
        components: {
            FilePond
        },
        data(){
            return {
                myFiles: []
            }
        },
        methods: {
            handleFilePondInit(){
                this.myFiles = [];

                let arr = this.form.image ? this.form.image.split('|') : [];

                for(let i =0; i < arr.length; i++){
                    this.myFiles.push({

                        source: '/' + arr[i],

                        options: {
                            type: 'local',
                            metadata: {
                                poster: '/' + arr[i]
                            }
                        }

                    });
                }


            },
            addFormImage(image)
            {
                let arr = this.form.image ? this.form.image.split('|') : [];
                arr.push(image);
                this.form.image = arr.join('|');
                console.log(this.form.image);
            },
            removeFormImage(image)
            {
                let arr = this.form.image ? this.form.image.split('|') : [];
                arr.remove(image);
                this.form.image = arr.join('|');
                console.log(this.form.image);
            },
            handleFilePondLoad(response)
            {
                this.addFormImage(response);
                return response;
            },
            handleFilePondRemove(source, load, error)
            {
                this.removeFormImage(source.replace(/^\//, ''));
                load();
            },
            handleFilePondRevert(uniqueId, load, error)
            {
                this.removeFormImage(uniqueId);
                axios.post('/upload-books-revert', {
                    image: uniqueId
                });
                load();
            }
        }
    }

    Array.prototype.remove = function() {
        var what, a = arguments, L = a.length, ax;
        while (L && this.length) {
            what = a[--L];
            while ((ax = this.indexOf(what)) !== -1) {
                this.splice(ax, 1);
            }
        }
        return this;
    };


</script>
