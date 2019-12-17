<template>
    <div>
        <div v-for="(reply ,index) in items" :key="reply.id">
            <reply :reply="reply" @deleted="remove(index)"></reply>
<!--            父组件中监听事件-->
        </div>

        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <p v-if="$parent.locked">
            This thread is locked.No more replies are allowed.
        </p>
        <new-reply @created="add" v-else></new-reply>
    </div>
</template>

<script>
    import Reply from './Reply';
    import NewReply from './NewReply';//new-reply
    import collection from '../mixins/Collection';//分页

    export default {
        components: { Reply,NewReply },

        mixins: [collection],//混入对象

        data() {
            return {
                dataSet:false,
            }
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch(page) {
                axios.get(this.url(page)).then(this.refresh);
            },
            url(page) {
                if (! page) {
                    let query = location.search.match(/page=(\d+)/);

                    page = query ? query[1] : 1;
                }

                return `${location.pathname}/replies?page=${page}`;
            },
            refresh({data}) {
                this.dataSet = data;
                this.items = data.data;

                window.scrollTo(0,0);//到最上方
            }
        }
    }
</script>
