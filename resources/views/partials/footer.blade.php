<footer class="border-t border-gray-200 bg-white">

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">

        <div class="grid gap-10 md:grid-cols-4">

            {{-- Brand --}}
            <div class="md:col-span-2">

                <a
                    href="{{ route('home') }}"
                    class="text-2xl font-bold"
                >
                    SignGyaan
                </a>

                <p class="mt-4 max-w-md text-sm leading-6 text-gray-600">
                    Accessible visual learning through Indian Sign Language.
                </p>

            </div>


            {{-- Learn --}}
            <div>

                <h2 class="text-sm font-semibold">
                    Learn
                </h2>

                <ul class="mt-4 space-y-3 text-sm text-gray-600">

                    <li>
                        <a
                            href="{{ route('learn') }}"
                            class="hover:text-gray-950"
                        >
                            Learn
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('subjects') }}"
                            class="hover:text-gray-950"
                        >
                            Subjects
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('explore') }}"
                            class="hover:text-gray-950"
                        >
                            Explore
                        </a>
                    </li>

                </ul>

            </div>


            {{-- SignGyaan --}}
            <div>

                <h2 class="text-sm font-semibold">
                    SignGyaan
                </h2>

                <ul class="mt-4 space-y-3 text-sm text-gray-600">

                    <li>
                        <a
                            href="{{ route('about') }}"
                            class="hover:text-gray-950"
                        >
                            About
                        </a>
                    </li>

                    <li>
                        <a
                            href="#"
                            class="hover:text-gray-950"
                        >
                            Contact
                        </a>
                    </li>

                    <li>
                        <a
                            href="#"
                            class="hover:text-gray-950"
                        >
                            Accessibility
                        </a>
                    </li>

                </ul>

            </div>

        </div>


        <div
            class="mt-10 border-t border-gray-200 pt-6 text-sm text-gray-500"
        >
            © {{ date('Y') }} SignGyaan. All rights reserved.
        </div>

    </div>

</footer>