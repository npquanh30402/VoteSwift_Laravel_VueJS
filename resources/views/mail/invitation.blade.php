{!! $content !!}

<div style="font-family: Arial, sans-serif;">
    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
        <tr style="background-color: #f8f9fa;">
            <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Sender:</strong></td>
            <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $sender->username }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Receiver:</strong></td>
            <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $receiver->username }}</td>
        </tr>
        <tr style="background-color: #f8f9fa;">
            <td style="padding: 10px; border: 1px solid #dee2e6;"><strong>Invitation URL:</strong></td>
            <td style="padding: 10px; border: 1px solid #dee2e6;"><a href="{{ $invitationUrl }}"
                                                                     style="color: #007bff; text-decoration: none;">Go
                    to invitation</a>
            </td>
        </tr>
        </tbody>
    </table>
</div>
