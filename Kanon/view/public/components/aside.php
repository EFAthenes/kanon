
<div class="offcanvas-body">
        <nav class="bd-links w-100" id="bd-docs-nav" aria-label="Docs navigation">
                <ul class="bd-links-nav list-unstyled mb-0 pb-3 pb-md-2 pe-lg-2">
                        <li class="bd-links-group py-2">
                                <strong class="bd-links-heading d-flex w-100 align-items-center fw-semibold">
                                        Getting started </strong>
                                <ul class="list-unstyled fw-normal pb-2 small">

                                    {% for compName in compNames %}

                                        <li>
                                                <a href="#{{compName}}" class="bd-links-link d-inline-block rounded active" aria-current="page"> {{compName}} </a>
                                        </li>
                                    {% endfor %} 
                                </ul>
                        </li>

                </ul>
        </nav>
</div>

