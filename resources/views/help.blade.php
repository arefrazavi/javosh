@extends($layout)

@section('title', trans('common_lang.Help'))
@section('content')
    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-info">
                <div class="box-header">
                    <h4> @lang('common_lang.Help_Gold_Summarization') </h4>
                </div>
                <div class="box-body">
                    <ol id="gold-selection-guide-description">
                        <li>
                            <span>
                                @lang("common_lang.Register_Login_Guide1")
                                <a class="btn-link" href="{{ route("auth.register.form") }}" >@lang("common_lang.Register_Login_Guide2")</a>
                                @lang("common_lang.Register_Login_Guide3")
                            </span>
                        </li>
                        <li>
                            <div>@lang("common_lang.Go_TO_Product_List")</div>
                            <div><span class="alert-lucky"> @lang("common_lang.Go_TO_10_lucky_products") </span></div>
                        </li>
                        <li>
                            <span>@lang("common_lang.Go_TO_Suggestion_Page")</span>
                        </li>
                        <li><span>@lang("common_lang.Aspect_Selection_Guide")</span></li>
                        <li>
                            <span>@lang("common_lang.Sentence_Selection_Guide")</span>
                            <ul class="no-list-style">
                                <li>
                                    <i class="fa fa-plus-circle"></i> : @lang("common_lang.Positive_Guide")
                                </li>
                                <li>
                                    <i class="fa fa-dot-circle-o"></i> : @lang("common_lang.Neutral_Guide")
                                </li>
                                <li>
                                    <i class="fa fa-minus-circle"></i> : @lang("common_lang.Negative_Guide")
                                </li>
                            </ul>
                        </li>
                        <li>
                            <span>@lang("common_lang.Important_Points")</span>
                            <ul class="list-style-checkmark">
                                <li><b> @lang("common_lang.Max_Summary_Size_Guide") </b></li>
                                <li><b> @lang("common_lang.Diversity_Guide") </b></li>
                                <li><b> @lang("common_lang.See_comments_list_On_Hover") </b></li>
                                <li><b> @lang("common_lang.No_need_to_fill_all") </b></li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
