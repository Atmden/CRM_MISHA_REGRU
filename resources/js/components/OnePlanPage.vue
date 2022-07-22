<template>
    <div class="wrapper-div">
        <div class="content-part">

            <div class="content-plan-page" v-if="ONE_PLAN_PAGE">
                <h1 class="title">{{ ONE_PLAN_PAGE.account_name }}</h1>
                <div class="nav-panel">
                    <div class="btn-view-content-plan">
                        <md-button class="btn btn-blue btn-filter" @click="changeView(false)">Обычный вид</md-button>
                        <md-button class="btn btn-blue btn-filter" @click="changeView(true)">Instagram</md-button>
                    </div>
                </div>
                <div v-if="!insta" class="post-block" v-for="(item, index) in ONE_PLAN_PAGE.plans">



                    <div class="post-date"><i class="demo-icon icon-calendar"></i> {{ item.date}}</div>

                    <div class="post-item" v-for="(item_plan, index_plan) in item.plans">

                        <div class="image" v-if="item_plan.images.length > 0 || item_plan.videos.length > 0 || item_plan.youtubes.length > 0">
                            <splide :options="slideOption(item_plan)" :ref="'splide_'+item_plan.id">
                                <splide-slide v-for="plan_image in item_plan.images" :key="plan_image.id" >
                                    <img :src="'/plan_images/'+plan_image.file">
                                </splide-slide>
                                <splide-slide v-for="plan_video in item_plan.videos" :key="plan_video.id">
                                    <vue-player class="video-player"
                                                :src="'/plan_videos/'+plan_video.file"
                                                :poster="'/plan_videos/'+plan_video.file+'.png'"
                                    ></vue-player>
                                </splide-slide>
                                <splide-slide v-for="plan_youtube in item_plan.youtubes" :key="plan_youtube.id">
                                    <iframe class="video-player" :src="plan_youtube.url" frameborder="0"
                                            allowfullscreen></iframe>
                                </splide-slide>
                            </splide>
                            <i class="demo-icon icon-zoom-in" @click="showSlider(item_plan)"></i>
                        </div>
                        <div class="desc">
                            <div class="info-panel">
                                <div class="left">
                                    <div v-if="item_plan.insta" class="soc">
                                        <i class="demo-icon icon-instagram"></i>
                                        <!--<i class="demo-icon icon-vk-reproducto"></i>-->
                                    </div>
                                    <div class="date"><i class="demo-icon icon-calendar2"></i> {{
                                        item.date}}
                                    </div>
                                    <div class="time"><i class="demo-icon icon-clock"></i> {{ item_plan.public_at_time}}
                                    </div>
                                </div>
                                <div class="right">
                                    <div v-if="item_plan.comment_apply > 0" class="check"><i class="demo-icon icon-checked"></i> {{ item_plan.comment_apply }}</div>

                                    <div v-if="item_plan.comment_no_apply > 0" class="notif"><i class="demo-icon icon-attention-sign"></i> {{ item_plan.comment_no_apply }}</div>
                                    <div><a @click="getlink(item_plan.id)" class="pointer" title="Получить ссылку на пост"><i class="demo-icon icon-exchange2"></i></a></div>
                                </div>
                            </div>
                            <h4>{{ item_plan.title }}</h4>
                            <p class="divemoji" v-html="item_plan.content" style="white-space: pre-line;">
                            </p>
                            <p>
                                <span style="color: #0babe5">
                                    {{ item_plan.hashtags }}
                                </span>
                            </p>
                            <hr>
                            <p>
                                <span v-for="plan_tag in item_plan.tags" style="color: #0babe5">
                                    #{{ plan_tag.name }}
                                </span>
                            </p>
                        </div>
                        <div class="nav-panel">
                            <div class="status status-green" :style="'background-color:'+item_plan.status.color+';'">
                                {{ item_plan.status.title }}
                            </div>
                            <md-button v-if="item_plan.status.id == 2" class="btn btn-blue"
                                       @click="applyPost(item_plan.id)"><i
                                    class="demo-icon icon-exchange2"></i> Утвердить
                            </md-button>
                            <md-button v-if="USER.can_edit" class="btn btn-blue" @click="editPopupShow(item_plan.id)"><i
                                    class="demo-icon icon-exchange2"></i> Изменить
                            </md-button>
                            <md-button class="btn btn-blue" @click="commentsPopupShow(item_plan.id)"><i
                                    class="demo-icon icon-comment"></i> Правки
                            </md-button>
                        </div>
                    </div>
                </div>
                <div v-if="insta" class="insta-block">
                    <div class="insta-panel">
                        <div class="insta-head">
                            <div class="title">Instagram</div>
                            <div class="title-account">{{ ONE_PLAN_PAGE.account_name }}</div>
                        </div>
                        <!--Плитка-->
                        <div v-if="!show_details_insta" class="insta-card-block row">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-6"
                                 v-for="(item_insta, index) in ONE_PLAN_PAGE.insta">
                                <div v-if="item_insta[0].images[0]" class="insta-card" :style="'background-image: url(/plan_images/'+item_insta[0].images[0].file+');'">
                                    <div class="date">{{ item_insta[0].public_at}}</div>
                                    <!--<img :src="'/plan_images/'+item_insta[0].images[0].file" alt="">-->
                                    <div class="btns">
                                        <md-button class="btn btn-blue btn-filter" @click="details(index)">Подробнее
                                        </md-button>
                                        <md-button class="btn btn-blue btn-filter"
                                                   @click="commentsPopupShow(item_insta[0].plan_id)">Правки
                                        </md-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Плитка-->

                        <div v-if="show_details_insta" class="insta-full-card">
                            <div class="insta-full-image">
                                <img :src="'/plan_images/' + detail_insta.images[0].file">
                            </div>
                            <div class="insta-full-desc">
                                <div class="date">{{ detail_insta.public_at }} {{ detail_insta.time_at }}</div>
                                <p v-html="detail_insta.content">
                                </p>
                                <md-button class="btn btn-blue btn-filter" @click="close_detail_insta">Назад</md-button>
                                <md-button class="btn btn-blue btn-filter"
                                           @click="commentsPopupShow(detail_insta.plan_id)">Правки
                                </md-button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <md-dialog :md-active.sync="LinkDialog" :md-click-outside-to-close="true">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <a :href="linkurl" target="_blank">{{linkurl}}</a>
                </div>
            </div>
        </md-dialog>
        <md-dialog :md-active.sync="sliderDialog" :md-click-outside-to-close="true">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <splide v-if="item_plan_sliderdialog != null" :options="slideOptionFull(item_plan_sliderdialog)" ref="splide_full">
                        <splide-slide v-for="plan_image in item_plan_sliderdialog.images" :key="plan_image.id" >
                            <img :src="'/plan_images/'+plan_image.file">
                        </splide-slide>
                        <splide-slide v-for="plan_video in item_plan_sliderdialog.videos" :key="plan_video.id">
                            <vue-player class="video-player"
                                        :src="'/plan_videos/'+plan_video.file"
                                        :poster="'/plan_videos/'+plan_video.file+'.png'"
                            ></vue-player>
                        </splide-slide>
                        <splide-slide v-for="plan_youtube in item_plan_sliderdialog.youtubes" :key="plan_youtube.id">
                            <iframe class="video-player" :src="plan_youtube.url" frameborder="0"
                                    allowfullscreen></iframe>
                        </splide-slide>
                    </splide>
                </div>
            </div>
        </md-dialog>
        <md-dialog :md-active.sync="newPostDialog" :md-click-outside-to-close="false">
            <h2>Создать публикацию</h2>
            <md-tabs md-dynamic-height>
                <md-tab md-label="Содержимое публикации">
                    <form novalidate class="md-layout form-post-edit" @submit.prevent="validateUser">
                        <div class="row">
                            <div class="col-lg-4 col-lg-offset-8">
                                <md-field>
                                    <label>Статус публикации</label>
                                    <multiselect
                                            v-model="new_plan.status"
                                            deselect-label="Вы можете только поменять статус"
                                            track-by="id"
                                            label="title"
                                            placeholder="Выберите статус"
                                            :options="STATUSES"
                                            :searchable="false"
                                            :allow-empty="false">
                                    </multiselect>
                                </md-field>
                            </div>
                            <div class="col-lg-4 col-lg-offset-8">
                                <br>
                                <md-field>
                                    <md-checkbox v-model="new_plan.insta" true-value="1" false-value="0">
                                        Вид инстаграма
                                    </md-checkbox>
                                </md-field>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <md-field :class="getValidationClass('firstName')">
                                    <label>Дата публикации</label>
                                    <date-picker v-model="new_plan.public_at" valueType="format"
                                                 format="DD/MM/YYYY"></date-picker>
                                </md-field>
                            </div>
                            <div class="col-lg-6">
                                <md-field :class="getValidationClass('firstName')">
                                    <label for="first-name">Время публикации</label>
                                    <vue-timepicker v-model="new_plan.time_at"
                                                    format="HH:mm"></vue-timepicker>
                                    <!--<md-input  v-model="form.time_pub" :disclearabled="sending" />-->
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field :class="getValidationClass('firstName')">
                                    <label for="first-name">Заголовок поста</label>
                                    <md-input class="title-post-input" v-model="new_plan.title"/>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field>
                                    <label>Содержимое поста</label>
                                    <!--<vue-mce :config="mceconfig" v-model="new_plan.content"/>-->
                                    <textarea class="emojionearea emojionearea_newpost" v-model="new_plan.content"></textarea>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field>
                                    <div v-for="(item, index) in ACCOUNT_SOCNETS">
                                        <md-checkbox v-model="new_plan.soc_net" :value=item.id>
                                            {{ item.name }}
                                        </md-checkbox>
                                    </div>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field>
                                    <label for="first-name">Хэштеги</label>
                                    <md-input class="hashtag-input" v-model="new_plan.hashtags"/>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field :class="getValidationClass('firstName')">
                                    <label for="first-name">Теги (для фильтра)</label>
                                    <vue-tags-input
                                            class="hashtag-input"
                                            v-model="tag"
                                            :tags="new_plan.tags"
                                            :autocomplete-items="autocompleteTags"
                                            @tags-changed="updateTags"
                                            placeholder="Теги"
                                    />
                                </md-field>
                            </div>

                        </div>
                    </form>
                </md-tab>
                <md-tab md-label="Фото/Видео">

                    <input-file
                            :random_number="new_plan.random_number"
                    ></input-file>

                </md-tab>
            </md-tabs>
            <md-dialog-actions>
                <md-button class="btn btn-cancel" @click="newPostDialog= false">Отмена</md-button>
                <md-button class="btn btn-blue" @click="newPlanSave">Сохранить</md-button>
            </md-dialog-actions>
        </md-dialog>
        <md-dialog :md-active.sync="editPopup" :md-click-outside-to-close="false">
            <h2>Изменить публикацию</h2>
            <md-tabs md-dynamic-height>
                <md-tab md-label="Содержимое публикации">
                    <form novalidate class="md-layout form-post-edit" @submit.prevent="validateUser">
                        <div class="row">
                            <div class="col-lg-4 col-lg-offset-8">
                                <md-field>
                                    <label for="">Статус публикации</label>
                                    <multiselect
                                            v-model="edit_plan.status"
                                            deselect-label="Вы можете только поменять статус"
                                            track-by="id"
                                            label="title"
                                            placeholder="Выберите статус"
                                            :options="STATUSES"
                                            :searchable="false"
                                            :allow-empty="false">
                                    </multiselect>
                                </md-field>

                            </div>
                            <div class="col-lg-4 col-lg-offset-8">
                                <br>
                                <md-field>
                                    <md-checkbox v-model="edit_plan.insta" true-value="1" false-value="0">
                                        Вид инстаграма
                                    </md-checkbox>
                                </md-field>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <md-field :class="getValidationClass('firstName')">
                                    <label>Дата публикации</label>
                                    <date-picker v-model="edit_plan.public_at" valueType="format"
                                                 format="DD/MM/YYYY"></date-picker>
                                </md-field>
                            </div>
                            <div class="col-lg-6">
                                <md-field :class="getValidationClass('firstName')">
                                    <label for="first-name">Время публикации</label>
                                    <vue-timepicker v-model="edit_plan.time_at"
                                                    format="HH:mm"></vue-timepicker>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field :class="getValidationClass('firstName')">
                                    <label for="first-name">Заголовок поста</label>
                                    <md-input class="title-post-input" v-model="edit_plan.title"/>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field>
                                    <label>Содержимое поста</label>
                                    <!--<vue-mce :config="mceconfig" v-model="edit_plan.content"/>-->
                                    <textarea class="emojionearea emojionearea_editpost" v-model="edit_plan.content"></textarea>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field>
                                    <div v-for="(item, index) in ACCOUNT_SOCNETS">
                                        <md-checkbox v-model="edit_plan.soc_net" :value=item.id>
                                            {{ item.name }}
                                        </md-checkbox>
                                    </div>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field>
                                    <label for="first-name">Хэштеги</label>
                                    <md-input class="hashtag-input" v-model="edit_plan.hashtags"/>
                                </md-field>
                            </div>
                            <div class="col-lg-12">
                                <md-field :class="getValidationClass('firstName')">
                                    <label for="first-name">Теги (для фильтра)</label>
                                    <vue-tags-input
                                            class="hashtag-input"
                                            v-model="tag"
                                            :tags="edit_plan.tags"
                                            :autocomplete-items="autocompleteTags"
                                            @tags-changed="updateEditTags"
                                            placeholder="Теги"
                                    />
                                </md-field>
                            </div>

                        </div>
                    </form>
                </md-tab>
                <md-tab md-label="Фото/Видео">
                    <edit-file
                            :current_images="edit_plan.images"
                            :current_videos="edit_plan.videos"
                            :current_youtubes="edit_plan.youtubes"
                            :plan_id="edit_plan.plan_id"
                    ></edit-file>
                </md-tab>
            </md-tabs>
            <md-dialog-actions>
                <md-button class="btn btn-cancel" @click="editPopup = false">Отмена</md-button>
                <md-button class="btn btn-blue" @click="saveEditPlan">Сохранить</md-button>
            </md-dialog-actions>
        </md-dialog>
        <md-dialog :md-active.sync="commentsPopup" :md-click-outside-to-close="false">
            <h2>Правки</h2>
            <div class="edits-block">
                <div class="edits-item" v-for="(comment, index_comment) in currentComments">
                    <div class="avatar"><img :src="'/user_avatars/' + comment.user.avatar" alt=""></div>
                    <div class="desc">
                        <p>{{ comment.content }}</p>
                        <div class="check">
                            <md-checkbox @change="changeApplyComment(index_comment)" v-model="comment.apply"
                                         true-value="1" false-value="0">Обработано
                            </md-checkbox>
                        </div>
                        <div class="info">
                            <div class="author">Автор правок</div>
                            <div class="date">{{ comment.created_at }}</div>
                            <div class="email">{{ comment.user.email }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <form novalidate class="md-layout form-user-edit">
                <md-field>
                    <label>Добавить новую правку</label>
                    <md-textarea v-model="newComment"></md-textarea>
                </md-field>
                <md-dialog-actions>
                    <md-button class="btn btn-cancel" @click="commentsPopup = false">Закрыть</md-button>
                    <md-button class="btn btn-blue" @click="saveComment">Добавить новую правку</md-button>
                </md-dialog-actions>
            </form>
        </md-dialog>
        <md-snackbar :md-active.sync="showSnackbar" md-persistent>
            <span>{{ snackBarMessage }}</span>
        </md-snackbar>
    </div>

</template>
<style lang="scss" scoped>
    .pointer {
        color: #477bf0;
        cursor: pointer;
    }
    .video-player {
        top: 50%;
        position: relative;
        background: #f7f7f7;
        transform: translate(0, -50%);
    }

    .md-dialog .md-dialog-container {
        max-width: 768px;
    }

    .insta-block {
        justify-content: center;
        display: flex;
        flex-wrap: wrap;
    }

    .insta-panel {
        width: 500px;
        -webkit-border-radius: 12px;
        -moz-border-radius: 12px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(219, 224, 235, .7);
        background-color: #fff;
    }

    .insta-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #DBE0EB;
        padding: 10px;
        margin-bottom: 10px;
    }

    .insta-head .title {
        font-weight: 900;
    }

    .insta-head .title-account {
        font-weight: 700;
    }

    .insta-card {
        height: 160px;
        border: 1px solid #DBE0EB;
        position: relative;
        background-position: center center;
        background-repeat: no-repeat;
        -webkit-background-size: cover;
        background-size: cover;
    }

    .insta-card .btns {
        position: absolute;
        bottom: 15px;
        left: 15px;
        right: 15px;
        visibility: hidden;
        opacity: 0;
        transition: all .3s;
    }

    .insta-card:hover .btns {
        visibility: visible;
        opacity: 1;
    }

    .insta-card .date {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        padding: 5px 10px;
        visibility: hidden;
        opacity: 0;
        text-align: center;
        font-size: 12px;
        color: #fff;
        font-weight: 700;
        background-color: rgba(0, 0, 0, .3);
        transition: all .3s;
    }

    .insta-card:hover .date {
        visibility: visible;
        opacity: 1;
    }


    .insta-card .btns .btn {
        width: 100%;
        margin-bottom: 0 !important;
        height: 34px !important;
        font-size: 12px !important;
        -webkit-border-radius: 3px !important;
        -moz-border-radius: 3px !important;
        border-radius: 3px !important;
        margin-top: 5px !important;
        min-width: auto !important;
    }

    .insta-card .btns .btn .md-button-content {
        font-size: 12px !important;
    }

    .insta-card-block {
        margin-left: -5px !important;
        margin-right: -5px !important;
        padding-left: 10px;
        padding-right: 10px;
    }

    .insta-card-block > div {
        padding-left: 5px !important;
        padding-right: 5px !important;
        margin-bottom: 10px;
    }


    .insta-full-card {
        padding: 10px;
    }

    .insta-full-card .insta-full-image {
        min-height: 490px;
        border: 1px solid #DBE0EB;
        margin-bottom: 10px;
    }

    .insta-full-card .date {
        color: #DBE0EB;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .insta-full-card p {
        font-size: 14px;
    }


    .edits-block {
        flex-basis: 100%;
    }

    .edits-item {
        padding: 15px;
        display: flex;
        margin-bottom: 15px;
        border-bottom: 1px solid #DBE0EB;
        background-color: #fff;
    }

    .edits-item .avatar {
        width: 80px;
        height: 80px;
        position: relative;
        border: 1px solid #DBE0EB;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .edits-item .avatar img {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%)
    }

    .edits-item .desc {
        font-size: 14px;
        padding-left: 30px;
    }

    .edits-item .info {
        font-size: 12px;
        color: #B5BED1;
        display: flex;
        align-items: center;
    }

    .edits-item .info > div {
        margin-right: 15px;
    }

</style>
<script>

    import {mapActions, mapGetters} from 'vuex'
    import {Splide, SplideSlide} from '@splidejs/vue-splide';
    import {validationMixin} from 'vuelidate'
    import VueTimepicker from 'vue2-timepicker'
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/locale/ru';
    import VueTagsInput from '@johmun/vue-tags-input';
    import vuePlayer from '@algoz098/vue-player'
    import {
        required,
        email,
        minLength,
        maxLength
    } from 'vuelidate/lib/validators'

    export default {

        name: 'OnePlanPage',

        components: {
            Splide,
            SplideSlide,
            VueTimepicker,
            VueTagsInput,
            DatePicker,
            vuePlayer
        },
        mixins: [validationMixin],

        data() {

            return {
                item_plan_sliderdialog:null,
                show_details_insta: false,
                detail_insta: [],
                insta: false,
                calendarData: null,
                showSnackbar: false,
                randomNumber: null,
                snackBarMessage: '',

                value: '',
                yourStringTimeValue: '',
                tag: '',
                autocompleteTags: [],
                notwatch: false,
                count_watch: 0,
                init_filter: 0,
                filter: {
                    status: [],
                    soc_net: [],
                    period: {
                        id: null
                    },
                    tags: [],
                    public_to: '',
                    public_from: '',
                },
                new_plan: {
                    account_id: null,
                    status: null,
                    insta: "0",
                    public_at: (new Date().toJSON().slice(0, 10).split('-').reverse().join('/')),
                    time_at: '00:00',
                    title: '',
                    hashtags: '',
                    content: '',
                    soc_net: [],
                    tags: [],
                    random_number: null,
                },
                edit_plan: {
                    plan_id: null,
                    status: null,
                    public_at: '',
                    time_at: '00:00',
                    title: '',
                    content: '',
                    soc_net: [],
                    tags: [],
                    random_number: null,
                },
                options: {
                    rewind: true,
                    width: 800,
                    type: 'slide',
                    perPage: 1,
                    focus: 'center',
                    arrows: true,
                },
                mceconfig: {
                    language: 'ru',
                    fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 22px 24px 26px 39px 34px 38px 42px 48px",
                    plugins: 'code print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help',
                    toolbar1: 'formatselect fontsizeselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat code',
                },
                linkurl:null,
                LinkDialog:false,
                showDialog: false,
                sliderDialog: false,
                newPostDialog: false,
                commentsPopup: false,
                editPopup: false,
                newComment: '',
                currentComments: '',
                currentComments_id: null,
                showFilter: false,
                filterPeriod: [
                    {
                        id: 1,
                        title: 'Предыдущий месяц'
                    },
                    {
                        id: 2,
                        title: 'Текущий месяц'
                    },
                    {
                        id: 3,
                        title: 'Следующий месяц'
                    },
                    {
                        id: 4,
                        title: 'Произвольный период'
                    },
                ],
                showDatePickerFiler: false,
            };
        },
        validations: {
            form: {
                firstName: {
                    required,
                    minLength: minLength(3)
                },
                lastName: {
                    required,
                    minLength: minLength(3)
                },
                age: {
                    required,
                    maxLength: maxLength(3)
                },
                gender: {
                    required
                },
                email: {
                    required,
                    email
                }
            }
        },
        computed: {
            ...mapGetters([
                'ONE_PLAN_PAGE',
                'STATUSES',
                'ACCOUNT_SOCNETS',
                'USER'
            ]),

        },
        watch: {
            'tag': 'initTags',
            'filter.soc_net': 'postFilter',
            'filter.tags': 'postFilter',
            'filter.status': 'postFilter',
            'filter.period': 'postFilter',
            'filter.public_to': 'postFilter',
            'filter.public_from': 'postFilter',

        },
        methods: {
            getlink(id){

                this.linkurl = 'https://abg-media.com/pages/oneplanpage/'+id;

                this.LinkDialog = true;

            },
            showSlider(item_plan) {
                this.item_plan_sliderdialog = item_plan;
                setTimeout(() => {
                    this.$refs['splide_full'].splide.refresh();
                }, 500);
                this.sliderDialog = true;
            },
            slideOptionFull(item_plan) {
                if ((item_plan.images.length + item_plan.videos.length + item_plan.youtubes.length) > 1)
                {
                    var  optionsslide = {
                        rewind: true,

                        width: 600,
                        type: 'loop',
                        perPage: 1,
                        focus: 'center',
                        arrows: true,
                        pagination: true,
                    };
                }
                else {
                    var optionsslide = {
                        rewind: true,
                        width: 600,
                        type: 'slide',
                        perPage: 1,
                        focus: 'center',
                        arrows: false,
                        pagination: false,
                    };
                }
                return optionsslide;
            },
            slideOption(item_plan) {
                if ((item_plan.images.length + item_plan.videos.length + item_plan.youtubes.length) > 1)
                {
                    var  optionsslide = {
                        rewind:true,
                        //width: 800,
                        type: 'loop',
                        perPage: 1,
                        focus: 'center',
                        arrows: true,
                        pagination: true,
                    };
                }
                else {
                    var optionsslide = {
                        rewind: true,
                        width: 800,
                        type: 'slide',
                        perPage: 1,
                        focus: 'center',
                        arrows: false,
                        pagination: false,
                    };
                }
                return optionsslide;
            },
            changeApplyComment(index) {
                axios.post('/api/apply_comment_plan', {
                    comment_id: this.currentComments[index].id,
                    apply: this.currentComments[index].apply
                }).then(res => {
                    this.snackBarMessage = res.data.message;
                }).finally(() => {
                    this.GET_ONE_PLAN_PAGE(this.$route.params.id);
                    this.showSnackbar = true;
                });
            },
            close_detail_insta() {
                this.show_details_insta = false;
            },
            details(index) {
                this.detail_insta = this.CONTENT_PLAN.insta[index][0];
                this.show_details_insta = true;
            },
            changeView(value) {
                this.insta = value;
                console.log(this.ONE_PLAN_PAGE);
            },
            postFilter() {

                if (!this.init_filter) {
                    this.count_watch = this.count_watch + 1;
                }

                if (this.init_filter) {

                    if (this.filter.period != null) {
                        if (this.filter.period.id != 4) {
                            this.filter.account_id = this.$route.params.id;
                            this.GET_CONTENT_PLAN_WITH_FILTER(this.filter);
                            this.snackBarMessage = 'Фильтр применен';
                            this.showSnackbar = true;
                        } else {
                            if (this.filter.public_to != null && this.filter.public_from != null) {
                                this.filter.account_id = this.$route.params.id;
                                this.GET_CONTENT_PLAN_WITH_FILTER(this.filter);
                                this.snackBarMessage = 'Фильтр применен';
                                this.showSnackbar = true;
                            }
                        }
                        if (this.filter.period.id == 4) {
                            this.showDatePickerFiler = true;
                        } else {
                            this.showDatePickerFiler = false;
                        }

                    } else {
                        this.filter.account_id = this.$route.params.id;
                        this.GET_CONTENT_PLAN_WITH_FILTER(this.filter);
                        this.snackBarMessage = 'Фильтр применен';
                        this.showSnackbar = true;
                    }
                } else {
                    if (this.count_watch > 5 && !this.init_filter) {
                        this.init_filter = 1;
                        this.count_watch = 0;
                    }
                }

            },
            toogleFilter: function () {

                this.showFilter = !this.showFilter;
                if (this.CONTENT_PLAN.filter != null && !this.init_filter) {
                    if (this.showFilter) {
                        this.filter = this.CONTENT_PLAN.filter;
                    }
                }
                if (this.CONTENT_PLAN.filter == null) {
                    this.init_filter = 1;
                }
            },
            saveEditPlan() {
                this.edit_plan.content = $(".emojionearea_editpost").data("emojioneArea").getText();
                let edit_id = this.edit_plan.plan_id;

                axios.post('/api/save_edit_plan', this.edit_plan).then(response => {
                    this.snackBarMessage = response.data.message;
                }).finally(() => {
                    this.showSnackbar = true;
                    this.GET_ONE_PLAN_PAGE(this.$route.params.id);
                    this.editPopup = false;
//                    this.$refs['splide_'+edit_id][0].splide.refresh();
                    setTimeout(() => {
                        this.$refs['splide_' + edit_id][0].splide.refresh();
                    }, 1000);
                });


            },
            applyPost(plan_id) {
                axios.get('/api/apply_plan/' + plan_id)
                    .then(response => {
                        this.snackBarMessage = response.data.message;
                    }).finally(() => {
                    this.showSnackbar = true;
                    this.GET_ONE_PLAN_PAGE(this.$route.params.id);
                });
            },
            editPopupShow(plan_id) {
                axios.get('/api/get_edit_plan/' + plan_id).then(response => {
                    this.edit_plan = response.data.data;
                }).finally(() => {
                    this.editPopup = true;
                    setTimeout(() => {
                        $('.emojionearea').emojioneArea({pickerPosition: "botom", autocomplete: false});
                    }, 1000);
                });
            },
            saveComment() {

                axios.post('/api/save_comment_plan', {
                    plan_id: this.currentComments_id,
                    comment: this.newComment
                }).then(res => {
                    this.snackBarMessage = res.data.message;
                    this.currentComments = res.data.data;
                }).finally(() => {
                    this.newComment = '';
                    this.showSnackbar = true;
                    this.GET_ONE_PLAN_PAGE(this.$route.params.id);
                });
            },
            commentsPopupShow(plan_id) {

                axios.get('/api/get_comments_plan/' + plan_id).then(response => {
                    this.currentComments = response.data.data;
                }).finally(() => {
                    this.currentComments_id = plan_id;
                    this.commentsPopup = true;
                });

            },
            newPlanSave() {
                this.new_plan.content = $(".emojionearea_newpost").data("emojioneArea").getText();
                axios.post('/api/add_new_plan', this.new_plan).then(response => {
                    this.snackBarMessage = response.data.message;
                }).finally(() => {
                    this.showSnackbar = true;
                    this.GET_CONTENT_PLAN_WITH_NULL(this.$route.params.id);
                    this.newPostDialog = false;
                });
            },
            openNewPostDialog() {
                this.new_plan = {
                    account_id: null,
                    status: null,
                    insta: "0",
                    public_at: (new Date().toJSON().slice(0, 10).split('-').reverse().join('/')),
                    time_at: '00:00',
                    title: '',
                    hashtags: '',
                    content: '',
                    soc_net: [],
                    tags: [],
                    random_number: null,
                };
                this.getRandomNumber();
                this.new_plan.account_id = this.$route.params.id;
                this.new_plan.random_number = this.randomNumber;
                setTimeout(() => {
                    $('.emojionearea').emojioneArea({pickerPosition: "botom", autocomplete: false});
                }, 1000);
                this.newPostDialog = true;

            },
            getRandomNumber() {
                this.randomNumber = this.$route.params.id + Math.floor(Math.random() * (90000) + 1)
            },

            initTags() {
                if (this.tag.length < 2) return;


                axios.post('/api/get_tags', {
                    tag: this.tag
                }).then(response => {
                    this.autocompleteTags = response.data.data.map(a => {
                        return {text: a.name};
                    });
                });

            },
            updateEditTags(newTags) {
                this.autocompleteTags = [];
                this.edit_plan.tags = newTags;
            },
            updateTags(newTags) {
                this.autocompleteTags = [];
                this.new_plan.tags = newTags;
            },
            ...mapActions([
                'GET_ONE_PLAN_PAGE',
                'GET_CONTENT_PLAN_WITH_NULL',
                'GET_CONTENT_PLAN_WITH_FILTER',
                'GET_STATUSES',
                'GET_PAGE_SOCNETS',
            ]),
            getValidationClass(fieldName) {
                const field = this.$v.form[fieldName]

                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            clearForm() {
                this.$v.$reset()
                this.form.firstName = null
                this.form.lastName = null
                this.form.age = null
                this.form.gender = null
                this.form.email = null
            },
            saveUser() {
                this.sending = true

                // Instead of this timeout, here you can call your API
                window.setTimeout(() => {
                    this.lastUser = `${this.form.firstName} ${this.form.lastName}`
                    this.userSaved = true
                    this.sending = false
                    this.clearForm()
                }, 1500)
            },
            validateUser() {
                this.$v.$touch()

                if (!this.$v.$invalid) {
                    this.saveUser()
                }
            }
        },
        mounted() {

            this.GET_ONE_PLAN_PAGE(this.$route.params.id);
            this.GET_PAGE_SOCNETS(this.$route.params.id);
            this.GET_STATUSES();

            console.log(this.ONE_PLAN_PAGE);

            Vue.material.locale = {
                dateFormat: 'dd/MM/yyyy',
                startYear: 1900,
                endYear: 2099,
                days: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
                shortDays: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
                shorterDays: ['В', 'П', 'В', 'С', 'Ч', 'П', 'С'],
                months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                shortMonths: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июнь', 'Июнь', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                shorterMonths: ['Я', 'Ф', 'М', 'А', 'М', 'Юн', 'Юл', 'А', 'С', 'О', 'Н', 'Д'],
                firstDayOfAWeek: 1,
                cancel: 'Отмена',
                confirm: 'Ok'
            };
        },
    }
</script>
