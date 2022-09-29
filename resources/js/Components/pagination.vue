<template>
    <div v-if="links.length > 3" class="flex flex-wrap mb-1">

        <template v-for="link in links">
            <a class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-blue-700"
                :class="{ 'bg-blue-700 text-white': link.active }"
               :href="link.url" v-html="link.label"
            ></a>

        </template>

    </div>
</template>

<script>
export default {
    props: ['links']
}
</script>
