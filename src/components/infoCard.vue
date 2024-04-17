<template>
    <div class="infoCard" v-loading="loading">
        <div class="infoCardTime">{{ friendlyTime }}</div>
        <div class="infoCardText">{{ row.text }}</div>
        <el-button
            v-if="deleteable && row.dataId != -1"
            type="danger"
            @click="handleDelete"
            >删除</el-button
        >
    </div>
</template>
<script>
import tapi from "./tapinterface.js";
export default {
    props: {
        row: {
            type: Object,
            required: true,
        },
        deleteable: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            friendlyTime: "",
            loading: false,
        };
    },
    methods: {
        handleDelete() {
            ElMessageBox.confirm("此操作将永久删除, 是否继续?", "提示", {
                confirmButtonText: "确定",
                cancelButtonText: "取消",
                type: "warning",
            })
                .then(() => {
                    this.loading = true;
                    let api = new tapi();
                    api.delete_incident(this.row.dataId)
                        .then((res) => {
                            if (res.status == "success") {
                                ElMessage({
                                    title: "删除",
                                    message: "事件已删除",
                                    type: "success",
                                });
                                this.$parent.getInfo();
                            } else {
                                ElMessage({
                                    title: "删除",
                                    message: "请检查您的网络连接",
                                    type: "error",
                                });
                            }
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                })
                .catch(() => {
                    ElMessage({
                        type: "info",
                        message: "已取消删除",
                    });
                });
        },
        processRow() {
            console.log(this.row);
            let date = new Date(this.row.timestamp * 1000);
            let Y = date.getFullYear() + "-";
            let M =
                (date.getMonth() + 1 < 10
                    ? "0" + (date.getMonth() + 1)
                    : date.getMonth() + 1) + "-";
            let D =
                (date.getDate() < 10 ? "0" + date.getDate() : date.getDate()) +
                " ";
            let h =
                (date.getHours() < 10
                    ? "0" + date.getHours()
                    : date.getHours()) + ":";
            let m =
                (date.getMinutes() < 10
                    ? "0" + date.getMinutes()
                    : date.getMinutes()) + ":";
            let s =
                date.getSeconds() < 10
                    ? "0" + date.getSeconds()
                    : date.getSeconds();
            this.friendlyTime = Y + M + D + h + m + s;
        },
    },
    mounted() {
        this.processRow();
    },
};
</script>
