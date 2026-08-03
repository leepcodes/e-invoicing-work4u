<script setup lang="ts">

    const plans=[
        {
            name:"Starter",
            price:"₱499",
                features:[
                "100 invoices/month",
                "Customer management",
                "Email support"
            ]
        },
        {
            name:"Professional",
            price:"₱999",
                features:[
                "Unlimited invoices",
                "Reports & analytics",
                "Priority support"
            ],
            popular:true
        },
        {
            name:"Enterprise",
            price:"₱1999",
            features:[
                "Advanced security",
                "Multiple users",
                "Dedicated support"
            ]
        }
    ]

</script>


<template>

    <section id="pricing" class="py-20 bg-white">

        <div class="max-w-7xl mx-auto px-6">


            <div class="text-center">

                <h2 class="text-4xl font-extrabold text-gray-900">
                    Simple Pricing
                </h2>
                <p class="mt-3 text-gray-600">
                    Choose the plan that fits your business.
                </p>

            </div>



        <div class="mt-12 grid lg:grid-cols-3 gap-8">


            <div
            v-for="plan in plans"
            :key="plan.name"
            class="
            relative
            rounded-3xl
            border
            p-8
            shadow-sm
            "
            :class="plan.popular ? 'border-blue-600 shadow-xl':'border-gray-200'"
            >


                <span
                v-if="plan.popular"
                class="
                absolute
                top-5
                right-5
                text-xs
                bg-blue-600
                text-white
                px-3
                py-1
                rounded-full
                "
                >
                Popular
                </span>


                <h3 class="text-2xl font-bold text-black">
                {{plan.name}}
                </h3>


                <p class="mt-4 text-4xl font-extrabold text-blue-600">
                {{plan.price}}
                </p>


                <ul class="mt-6 space-y-3">

                <li
                v-for="item in plan.features"
                :key="item"
                class="text-gray-600"
                >
                ✓ {{item}}
                </li>

                </ul>


                <button
                class="
                mt-8
                w-full
                py-3
                rounded-xl
                bg-gradient-to-r
                from-blue-600
                to-indigo-600
                text-white
                font-semibold
                "
                >
                Choose Plan
                </button>


            </div>


        </div>


        </div>

    </section>

</template>
