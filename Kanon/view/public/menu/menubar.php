<nav class="navbar navbar-expand-lg bg-body-tertiary container-xxl bd-gutter flex-wrap flex-lg-nowrap">
    <div class="container-fluid">
        <a class="navbar-brand p-0 me-0 me-lg-2" href="?r=home">
            <img src="img/Kanov_logo2.png" height="60px;" />
            Κανών - Kanon
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"><i class="fa-solid fa-bars"></i></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="vr d-none d-lg-flex h-200 mx-lg-2 text-white"></div>  
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="?r=home#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?r=documentation">Documentation</a>
                </li>        
                <li class="nav-item">
                    <a class="nav-link" href="?r=components">Components</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?r=projects">Projects</a>
                </li>  
                
                <li class="nav-item">

                    
                </li> 

            </ul>
            
            
            <a class="nav-link" target="_blank" href="https://github.com/EFAthenes/kanon">The code <i class="fa-brands fa-github"></i></a>

            
            {% if languages is not empty and languages|length > 1 %}
            <ul class="navbar-nav ">
                <li class="nav-item dropdown">
                    <a class="navbar-menu-item nav-link navbar-light dropdown-toggle" href="" data-bs-toggle="dropdown" aria-label="Open Profile Menu" aria-expanded="false">
                        <i class="fa-solid fa-language"></i>

                    </a>  

                    <ul class="dropdown-menu settings-menu dropdown-menu-left">             
                        {% for lang_label,lang_option in languages %}
                        <li class="nav-item dropdown">
                            <a class="navbar-submenu-item nav-link " href="{{lang_option.url}}">
                                <div class=" k_lang_proposed k_lang_{{lang_option.tag}}">  
                                    {{lang_label}}
                                </div>
                            </a> 
                        </li>
                        {% endfor %}
                    </ul>                

                </li> 
            </ul> 
            {% endif %} 


        </div>
    </div>
</nav>