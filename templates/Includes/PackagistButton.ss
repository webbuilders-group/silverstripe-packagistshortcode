<p class="packagistButtonGroup">
    <span class="packagistButton packagistDownloadsButton">
        <a href="https://packagist.org/packages/$Package.ATT" title="$Pockage.ATT downloads on packagist.org" class="packagistDownloadsButton" target="_blank"><!-- --></a>
        <span class="count">
            <% if $DisplayMode=='monthly' %>
                $MonthlyDownloads
            <% else_if $DisplayMode=='daily' %>
                $DailyDownloads
            <% else %>
                $TotalDownloads
            <% end_if %>
        </span>
    </span>
</p>