<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

const products = [
    { id: 1, name: "EXAMPLE 1", image: "/images/carousel/1.png" },
    { id: 2, name: "EXAMPLE 2", image: "/images/carousel/2.png" },
    { id: 3, name: "EXAMPLE 3", image: "/images/carousel/3.png" },
    { id: 4, name: "EXAMPLE 4", image: "/images/carousel/4.png" },
    { id: 5, name: "EXAMPLE 5", image: "/images/carousel/5.png" },
    { id: 6, name: "EXAMPLE 6", image: "/images/carousel/6.png" },
    { id: 7, name: "EXAMPLE 7", image: "/images/carousel/7.png" },
    { id: 8, name: "EXAMPLE 8", image: "/images/carousel/8.png" },
    { id: 9, name: "EXAMPLE 9", image: "/images/carousel/9.png" },
    { id: 10, name: "EXAMPLE 10", image: "/images/carousel/10.png" },
];

const currentSlide = ref(0);

let interval: ReturnType<typeof setInterval>;

onMounted(() => {
    interval = setInterval(() => {
        currentSlide.value =
            (currentSlide.value + 1) % products.length;
    }, 5000);
});

onUnmounted(() => {
    clearInterval(interval);
});
</script>

<template>
    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-6">

            <!-- Title -->
            <h1 class="pb-8 text-center text-4xl font-extrabold text-gray-900">
                System Overview
            </h1>

            <!-- Carousel -->
            <div class="overflow-hidden">
                <div
                    class="flex transition-transform duration-1000 ease-in-out"
                    :style="{
                        transform: `translateX(-${currentSlide * 100}%)`
                    }"
                >
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="flex min-w-full justify-center px-4"
                    >
                        <img
                            :src="product.image"
                            :alt="product.name"
                            class="h-auto w-full max-w-5xl rounded-xl object-contain shadow-lg"
                        />
                    </div>
                </div>
            </div>

            <!-- Slide Indicator -->
            <div class="mt-6 flex justify-center gap-2">
                <button
                    v-for="(_, index) in products"
                    :key="index"
                    @click="currentSlide = index"
                    class="h-2 rounded-full transition-all"
                    :class="
                        currentSlide === index
                            ? 'w-8 bg-blue-600'
                            : 'w-2 bg-gray-300'
                    "
                />
            </div>

        </div>
    </section>
</template>
