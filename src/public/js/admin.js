// 全角スペースを削除し、インデントを修正
document.addEventListener('DOMContentLoaded', () => {
    const detailButtons = document.querySelectorAll('.detail-button');
    const modal = document.getElementById('modal');
    const modalCloseBtn = document.querySelector('.modal__close-btn');
    const deleteButton = document.querySelector('.delete-button');

    detailButtons.forEach(button => {
        button.addEventListener('click', () => {

            deleteButton.dataset.id = button.dataset.id;

            document.getElementById('modal-name').textContent = `${button.dataset.last_name} ${button.dataset.first_name}`;
            document.getElementById('modal-gender').textContent = button.dataset.gender;
            document.getElementById('modal-email').textContent = button.dataset.email;
            document.getElementById('modal-tel').textContent = button.dataset.tel;
            document.getElementById('modal-address').textContent = button.dataset.address;
            document.getElementById('modal-building').textContent = button.dataset.building;
            document.getElementById('modal-contact-type').textContent = button.dataset.contact_type;
            document.getElementById('modal-detail').textContent = button.dataset.detail;
            modal.style.display = 'flex';
        });
    });

    modalCloseBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    deleteButton.addEventListener('click', async () => {
        const contactId = deleteButton.dataset.id;

        // 削除確認のカスタムダイアログを実装
        if (window.confirm('本当にこの問い合わせを削除しますか？')) {
            try {
                // CSRFトークンを取得
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch(`/admin/delete/${contactId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        // CSRFトークンをヘッダーに追加
                        'X-CSRF-TOKEN': csrfToken
                    },
                });


                if (response.ok) {
                    alert('削除が完了しました。');
                    location.reload(); // ページをリロードして最新のデータを表示
                } else {
                    alert('削除に失敗しました。');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('削除中にエラーが発生しました。');
            }
        }
    });
});
