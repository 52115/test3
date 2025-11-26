#!/usr/bin/env python3
"""
ER図を生成するPythonスクリプト
Pillowを使用してER図を画像として出力します
"""

import os
try:
    from PIL import Image, ImageDraw, ImageFont
except ImportError:
    print("Pillowライブラリをインストール中...")
    os.system("pip3 install Pillow")
    from PIL import Image, ImageDraw, ImageFont

# 画像サイズを設定
width = 2400
height = 3200
img = Image.new('RGB', (width, height), color='white')
draw = ImageDraw.Draw(img)

# フォントを設定（デフォルトフォントを使用）
try:
    font_title = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 20)
    font_table = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 14)
    font_key = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 12)
except:
    font_title = ImageFont.load_default()
    font_table = ImageFont.load_default()
    font_key = ImageFont.load_default()

# テーブル定義
tables = {
    'users': {
        'x': 100, 'y': 50, 'width': 300, 'height': 280,
        'fields': ['id (PK)', 'name', 'email (UK)', 'email_verified_at', 'password', 
                   'remember_token', 'profile_image', 'postal_code', 'address', 
                   'building_name', 'created_at', 'updated_at']
    },
    'items': {
        'x': 500, 'y': 50, 'width': 350, 'height': 320,
        'fields': ['id (PK)', 'user_id (FK)', 'name', 'description', 'brand_name',
                   'price', 'condition', 'image_url', 'buyer_id (FK)', 
                   'created_at', 'updated_at']
    },
    'categories': {
        'x': 950, 'y': 50, 'width': 250, 'height': 180,
        'fields': ['id (PK)', 'name', 'created_at', 'updated_at']
    },
    'comments': {
        'x': 100, 'y': 400, 'width': 300, 'height': 220,
        'fields': ['id (PK)', 'user_id (FK)', 'item_id (FK)', 'content',
                   'created_at', 'updated_at']
    },
    'favorites': {
        'x': 500, 'y': 400, 'width': 300, 'height': 220,
        'fields': ['id (PK)', 'user_id (FK)', 'item_id (FK)',
                   'created_at', 'updated_at', 'UNIQUE(user_id, item_id)']
    },
    'purchases': {
        'x': 900, 'y': 400, 'width': 350, 'height': 300,
        'fields': ['id (PK)', 'user_id (FK)', 'item_id (FK)', 'payment_method',
                   'postal_code', 'address', 'building_name',
                   'stripe_payment_intent_id', 'created_at', 'updated_at']
    },
    'item_category': {
        'x': 500, 'y': 700, 'width': 300, 'height': 220,
        'fields': ['id (PK)', 'item_id (FK)', 'category_id (FK)',
                   'created_at', 'updated_at', 'UNIQUE(item_id, category_id)']
    }
}

# テーブルを描画
def draw_table(name, info):
    x, y = info['x'], info['y']
    w, h = info['width'], info['height']
    
    # テーブル名の背景
    draw.rectangle([x, y, x + w, y + 40], fill='#4A90E2', outline='black', width=2)
    draw.text((x + 10, y + 10), name, fill='white', font=font_title)
    
    # テーブル本体
    draw.rectangle([x, y + 40, x + w, y + h], fill='#E8F4FD', outline='black', width=2)
    
    # フィールドを描画
    field_y = y + 50
    for field in info['fields']:
        draw.text((x + 10, field_y), field, fill='black', font=font_table)
        field_y += 25
        if field_y > y + h - 10:
            break

# すべてのテーブルを描画
for name, info in tables.items():
    draw_table(name, info)

# リレーションシップを描画
relationships = [
    # users -> items (user_id)
    ((250, 330), (650, 100), '1:N'),
    # users -> items (buyer_id)
    ((350, 330), (825, 150), '0..1:N'),
    # users -> comments
    ((200, 330), (150, 400), '1:N'),
    # users -> favorites
    ((250, 330), (550, 400), '1:N'),
    # users -> purchases
    ((350, 330), (1000, 400), '1:N'),
    # items -> comments
    ((675, 370), (200, 400), '1:N'),
    # items -> favorites
    ((675, 370), (600, 400), '1:N'),
    # items -> purchases
    ((850, 370), (1050, 400), '1:N'),
    # items -> item_category
    ((675, 370), (650, 700), '1:N'),
    # categories -> item_category
    ((1075, 230), (650, 700), '1:N'),
]

# 矢印とラベルを描画
for (start, end, label) in relationships:
    # 線を描画
    draw.line([start, end], fill='black', width=2)
    
    # 矢印の先端を描画（簡易版）
    dx = end[0] - start[0]
    dy = end[1] - start[1]
    length = (dx**2 + dy**2)**0.5
    if length > 0:
        unit_x = dx / length
        unit_y = dy / length
        
        # 矢印の先端
        arrow_size = 15
        arrow_x = end[0] - unit_x * arrow_size
        arrow_y = end[1] - unit_y * arrow_size
        
        # 矢印の左右の点
        perp_x = -unit_y * 8
        perp_y = unit_x * 8
        
        arrow_points = [
            end,
            (arrow_x + perp_x, arrow_y + perp_y),
            (arrow_x - perp_x, arrow_y - perp_y)
        ]
        draw.polygon(arrow_points, fill='black')
        
        # ラベルを描画
        mid_x = (start[0] + end[0]) // 2
        mid_y = (start[1] + end[1]) // 2
        label_bbox = draw.textbbox((0, 0), label, font=font_key)
        label_w = label_bbox[2] - label_bbox[0]
        label_h = label_bbox[3] - label_bbox[1]
        draw.rectangle([mid_x - label_w//2 - 5, mid_y - label_h//2 - 3,
                       mid_x + label_w//2 + 5, mid_y + label_h//2 + 3],
                      fill='white', outline='black')
        draw.text((mid_x - label_w//2, mid_y - label_h//2), label,
                 fill='black', font=font_key)

# タイトルを追加
draw.text((width//2 - 100, 10), 'ER Diagram - Flea Market Database',
         fill='black', font=font_title)

# 画像を保存
output_file = 'ER図.png'
img.save(output_file)
print(f"ER図が生成されました: {output_file}")
