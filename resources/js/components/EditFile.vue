<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="input-file">

                <input type="file" id="file" @change="onInputChange" multiple>
                <ul>
                    <draggable v-model="uploaded_images" group="people" @start="drag=true" @end="drag=false" class="div-image"
                               :component-data="getComponentData()">
                    <li v-for="(item, index) in uploaded_images">
                        <div class="upload-image">
                            <img :src="'/plan_images/'+item.file">
                            <i class="demo-icon icon-checked"></i>
                            <div class="buttons-image">
                                <i class="demo-icon icon-trash" @click="deleteImageFromServer(item.id)"></i>
                            </div>
                        </div>
                    </li>
                    </draggable>

                    <li v-for="(item, index) in images">
                        <div class="upload-image">
                            <img :src="item">
                            <div class="buttons-image">
                                <i class="demo-icon icon-trash" @click="deleteImage(index)"></i>
                            </div>
                        </div>
                    </li>

                    <li>
                        <div class="select-image">
                            <label for="file" class="text-center"><i class="demo-icon icon-add"></i></label>
                        </div>
                    </li>
                </ul>
                <div class="loader" v-if="loading"></div>
                <md-button class="btn btn-blue" @click="upload" >Загрузить</md-button>
            </div>
        </div>
        <div class="col-lg-6">
            <h3>Видео</h3>

            <div class="input-file">
                <input style="display: none;" type="file" name="video" id="video" ref="video"
                       v-on:change="handleVideoUpload" multiple/>
                <ul>
                    <draggable v-model="uploaded_videos" group="people" @start="drag=true" @end="drag=false" class="div-image"
                               :component-data="getComponentVData()">
                    <li v-for="(item, index) in uploaded_videos">
                        <div class="upload-image">
                            <vue-player
                                    :src="'/plan_videos/'+item.file"
                                    :poster="'/plan_videos/'+item.file+'.png'"
                            ></vue-player>
                            <i class="demo-icon icon-checked"></i>
                            <div class="buttons-image">
                                <i class="demo-icon icon-trash" @click="deleteVideoFromServer(item.id)"></i>
                            </div>
                        </div>
                    </li>
                    </draggable>

                    <li v-for="(item, index) in videos">
                        <div class="upload-image">
                            <label for="video" class="text-center"><i
                                    class="demo-icon icon-upload-cloud-outline"></i></label>

                            <div class="buttons-image">
                                <i class="demo-icon icon-trash" @click="deleteVideo(index)"></i>
                            </div>
                        </div>
                    </li>

                    <li>
                        <div class="select-image">
                            <label for="video" class="text-center"><i class="demo-icon icon-video"></i></label>
                        </div>
                    </li>
                </ul>
                <div class="loader" v-if="loading_video"></div>
                <md-button :disabled="videos.length < 1" class="btn btn-blue" @click="uploadVideo">Загрузить видео
                </md-button>
                <br>
                <progress v-show="progress" max="100" :value.prop="uploadPercentage"></progress>

            </div>
        </div>
        <div class="col-lg-6">
            <h3>Youtube</h3>

            <div>
                <ul>
                    <li v-for="(item, index) in youtubes">
                        <a :href="item.url" target="_blank">{{ item.url }}</a>
                        <i class="demo-icon icon-trash" @click="deleteYoutubeFromServer(item.id)"></i>
                    </li>
                </ul>


                <md-field>
                    <label>URL видео</label>
                    <md-input class="title-post-input" v-model="url_youtube"/>
                </md-field>
                <div class="loader" v-if="loading_youtube"></div>
                <md-button :disabled="url_youtube.length < 1" class="btn btn-blue" @click="addYotubeVideo">
                    Добавить видео
                </md-button>
                <br>

            </div>
        </div>
    </div>

</template>
<style>
    .select-image:hover, .select-image label:hover {
        cursor: pointer;
    }
    .loader { /* Loader Div Class */
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #eceaea;
        background-image: url('../../img/ajax-loader.gif');
        background-size: 50px;
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
    }

    .buttons-image i:hover {
        color: #0094ff;
    }

    .buttons-image i {
        font-size: 16px;
    }

    .upload-image:hover .buttons-image {
        opacity: 1;
    }

    .buttons-image {
        width: 100%;
        z-index: 10;
        height: 30px;
        background: rgba(40, 49, 70, 0.75);
        position: absolute;
        bottom: 0;
        opacity: 0;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .upload-image {
        background: white;
        width: 100%;
        height: 100%;
        margin: 0;
        border: 2px solid rgba(40, 49, 70, 0.75);
        border-radius: 5px;
        position: relative;
    }

    .upload-image img {
        max-height: 100%;
        max-width: 100%;
        top: 0;
        left: 0;
        position: absolute;
        margin: auto;
        right: 0;
        bottom: 0;
        width: auto;
        height: auto;
        padding: 5px;
    }

    .input-file {
        float: left;
    }

    .input-file ul {
        display: flex;
        flex-wrap: wrap;
    }

    .input-file ul li {
        width: 160px;
        height: 120px;
        background: #e3e2e24f;
        border-radius: 5px;
        margin: 0 15px 15px 0;
        padding: 0;
        line-height: 0;
        list-style: none;
    }

    .input-file ul li::before {
        content: none;
    }

    .input-file ul li::after {
        content: none;
    }

    .input-file li > div {
        width: 100%;
        height: 100%;
        margin: 0;
    }

    .input-file li > div > label {
        width: 100%;
        height: 100%;
        display: block;
        margin: 0;
    }

    .input-file li > div > label > i {
        top: 50%;
        transform: translate(0, -50%);
        position: relative;
        color: #0094ff;
        font-size: 32px;
    }

    .input-file input {
        display: none;
    }

    .div-image {
        display: flex;
    }

    .div-add-image {
        border: 4px solid #0babe5;
        border-radius: 30px;
        padding: 10px;
        width: 200px;
        margin: 10px;
    }

    .svg-image {
        filter: invert(100%) sepia(93%) saturate(2220%) hue-rotate(86deg) brightness(115%) contrast(65%);
    }
</style>
<script>
    import vuePlayer from '@algoz098/vue-player'
    import draggable from 'vuedraggable'
    export default {
        components: {
            vuePlayer,
            draggable,

        },

        props: {
            plan_id: {default: null},
            current_images: {default: []},
            current_videos: {default: []},
            current_youtubes: {default: []}
        },
        data() {
            return {
                success: false,
                error: false,
                loading: false,
                loading_video: false,
                loading_youtube: false,
                files: [],
                images: [],
                uploaded_images: this.current_images,
                uploaded_videos: this.current_videos,
                youtubes: this.current_youtubes,
                uploadPercentage: 0,
                video: '',
                videos: [],
                filesv: [],
                progress: false,
                playing: true,
                url_youtube: '',

            }
        },
        methods: {
            handleVSort(e) {
                let element = this.filesv[e.oldIndex];
                this.filesv.splice(e.oldIndex, 1);
                this.filesv.splice(e.newIndex, 0, element);

                this.$emit('sortEditVideo',{ videos: this.uploaded_videos});
                //this.$parent.upload_images_main = this.uploaded_images;
                console.log(this.uploaded_videos);
            },
            getComponentVData() {
                return {
                    on: {
                        sort: this.handleVSort
                    }
                };
            },
            handleSort(e) {
                let element = this.files[e.oldIndex];
                this.files.splice(e.oldIndex, 1);
                this.files.splice(e.newIndex, 0, element);

                this.$emit('sortEditImage',{ images: this.uploaded_images});
                //this.$parent.upload_images_main = this.uploaded_images;
                console.log(this.uploaded_images);
            },
            getComponentData() {
                return {
                    on: {
                        sort: this.handleSort
                    }
                };
            },
            deleteImageFromServer(index) {
                this.loading = true;
                axios.get('/api/plan_images/delete/' + index)
                    .then(response => {
                        this.uploaded_images = response.data.data;
                    }).finally(() => {
                    this.loading = false;
                });
            },
            deleteVideoFromServer(index) {
                this.loading_video = true;
                axios.get('/api/plan_videos/delete/' + index)
                    .then(response => {
                        this.uploaded_videos = response.data.data;
                    }).finally(() => {
                    this.loading_video = false;
                });
            },
            deleteImage(index) {
                this.images.splice(index, 1);
                this.files.splice(index, 1);
            },
            deleteVideo(index) {
                this.videos.splice(index, 1);
                this.filesv.splice(index, 1);
            },
            onInputChange(e) {
                const files = e.target.files;
                Array.from(files).forEach(file => this.addImage(file));
            },
            addImage(file) {
                if (!file.type.match('image.*')) {
                    this.$toastr.e(`${file.name} is not an image`);
                    return;
                }

                this.files.push(file);

                const img = new Image(),
                    reader = new FileReader();

                reader.onload = (e) => this.images.push(e.target.result);

                reader.readAsDataURL(file);
            },
            upload() {
                this.loading = true;
                const formData = new FormData();
                formData.append('plan_id', this.plan_id);
                this.files.forEach(file => {
                    formData.append('images[]', file, file.name);
                });
                axios.post('/api/upload_images', formData)
                    .then(response => {
                        this.uploaded_images = response.data.data;
                        this.images = [];
                        this.files = [];
                    }).finally(() => {
                    this.loading = false;


                });
            },
            addYotubeVideo() {
                this.loading_youtube = true;
                const formData = new FormData();
                formData.append('plan_id', this.plan_id);
                formData.append('url_youtube', this.url_youtube);
                axios.post('/api/add_youtube', formData)
                    .then(response => {
                        this.youtubes = [];
                        this.youtubes.push.apply(this.youtubes, response.data.data);
                        this.url_youtube = '';
                    }).finally(() => {
                    this.loading_youtube = false;
                });
            },
            deleteYoutubeFromServer(index) {
                this.loading_youtube = true;
                axios.get('/api/plan_youtube/delete/' + index)
                    .then(response => {
                        this.youtubes = [];
                        this.youtubes.push.apply(this.youtubes, response.data.data);
                        this.url_youtube = '';
                    }).finally(() => {
                    this.loading_youtube = false;
                });
            },
            uploadVideo() {
                this.loading_video = true;
                this.progress = true;
                const formData = new FormData();
                formData.append('plan_id', this.plan_id);
                this.filesv.forEach(file => {
                    formData.append('videos[]', file, file.name);
                });
                axios.post('/api/upload_videos', formData, {
                    onUploadProgress: function (progressEvent) {
                        this.uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100));
                    }.bind(this)
                })
                    .then(response => {
                        this.uploaded_videos = [];
                        this.uploaded_videos.push.apply(this.uploaded_videos, response.data.data);
                        this.videos = [];
                        this.filesv = [];
                    }).finally(() => {
                    this.progress = false;
                    this.loading_video = false;


                });

            },
            handleVideoUpload(e) {
                const files = e.target.files;
                Array.from(files).forEach(file => this.addVideo(file));
            },
            addVideo(file) {
                if (!file.type.match('video.*')) {
                    this.$toastr.e(`${file.name} is not an video`);
                    return;
                }

                this.filesv.push(file);


                const img = new Image(),
                    reader = new FileReader();

                reader.onload = (e) => this.videos.push(e.target.result);

                reader.readAsDataURL(file);




            },
        }
    }
</script>
