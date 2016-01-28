<div class="panel-body">
    <div class="pull-left col-md-6">
        <header class="interior headermate">
            <h3>STREAMING MEDIA:</h3>
        </header>
        <div class="section">
            <table class="simple plan-details">
                <tbody>
                    <tr class="subscription-started-at">
                        <th>Feature: </th>
                        <td>
                            {{  @isset($media['storage']['featureCdn']) ?  $media['storage']['featureCdn'] : '0 B' }}
                        </td>
                    </tr>
                    <tr class="current-balance">
                        <th>Trailer: </th>
                        <td>
                            {{  @isset($media['storage']['trailerCdn']) ? $media['storage']['trailerCdn'] : '0 B' }}
                        </td>
                    </tr>
                    <tr class="current-balance">
                        <th>Extra: </th>
                        <td data-id="7140708" data-shortname="cinehost" class="next-billing-amount">
                            {{  @isset($media['storage']['extraCdn']) ? $media['storage']['extraCdn'] : '0 B' }}
                        </td>
                    </tr>
                    <tr class="current-balance">
                        <th>Total: </th>
                        <td data-id="7140708" data-shortname="cinehost" class="next-billing-amount">
                            {{  @isset($media['storage']['totalCdn']) ? $media['storage']['totalCdn'] : '0 B' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="pull-left col-md-6">
        <header class="interior headermate">
            <h3>MEZZANINE MEDIA:</h3>
        </header>
        <div class="section">
            <table class="simple plan-details">
                <tbody>
                    <tr class="subscription-started-at">
                        <th>Feature: </th>
                        <td>
                            {{  @isset($media['storage']['featureMez']) ? $media['storage']['featureMez'] : '0 B' }}
                        </td>
                    </tr>
                    <tr class="current-balance">
                        <th>Trailer: </th>
                        <td>
                            {{  @isset($media['storage']['trailerMez']) ? $media['storage']['trailerMez'] : '0 B' }}
                        </td>
                    </tr>
                    <tr class="current-balance">
                        <th>Extra: </th>
                        <td data-id="7140708" data-shortname="cinehost" class="next-billing-amount">
                            {{  @isset($media['storage']['extraMez']) ? $media['storage']['extraMez'] : '0 B' }}
                        </td>
                    </tr>
                    <tr class="current-balance">
                        <th>Total: </th>
                        <td data-id="7140708" data-shortname="cinehost" class="next-billing-amount">
                            {{  @isset($media['storage']['totalMez']) ? $media['storage']['totalMez'] : '0 B' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>