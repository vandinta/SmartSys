# Import Umum
import math
import re
from os import path
from datetime import datetime
import mysql.connector

# Data Fetcher
import pandas as pd

# Numerical
import numpy as np

# Accuracy
from sklearn.metrics import accuracy_score, mean_squared_error, mean_absolute_error

# Scaler
from sklearn.preprocessing import MinMaxScaler

# Model, LSTM
from keras.models import Sequential, load_model, model_from_json
from keras.layers import Dense, LSTM
from keras.preprocessing.sequence import TimeseriesGenerator

# Graph plot
import matplotlib.pyplot as plt

# Get Variable
import sys

# MAIN PROGRAM
def main():
  df = pd.read_csv('c:/xampp/htdocs/SmartSys/public/dataset/' + sys.argv[1] + '.csv', index_col='tanggal',parse_dates=True)
  df.index.freq='MS'
  
  dataset_12 = df.iloc[:12]
  dataset_prediksi = df.iloc[:]
  
  scaler = MinMaxScaler()
  scaler.fit(dataset_12)
  scaled_dataset_12 = scaler.transform(dataset_12)
  scaled_dataset_prediksi = scaler.transform(dataset_prediksi)
  
  n_input = 12
  n_features = 1
  
  json_file = open('c:/xampp/htdocs/SmartSys/public/model/' + sys.argv[2] + '.json', 'r')
  model_json = json_file.read()
  json_file.close()
  model = model_from_json(model_json)
  model.load_weights('c:/xampp/htdocs/SmartSys/public/model/' + sys.argv[2] + '.h5')
  
  model.compile(optimizer='adam', loss='mse')
  
  dataset_prediksi_pembelian = []

  first_eval_batch = scaled_dataset_12[-n_input:]
  
  current_batch = first_eval_batch.reshape((1, n_input, n_features))

  for i in range(len(dataset_prediksi)):
      
      # get the prediction value for the first batch
      current_pred = model.predict(current_batch)[0]
      
      # append the prediction into the array
      dataset_prediksi_pembelian.append(current_pred) 
      
      # use the prediction to update the batch and remove the first value
      current_batch = np.append(current_batch[:,1:,:],[[current_pred]],axis=1)
  
  true_dataset_prediksi_pembelian = scaler.inverse_transform(dataset_prediksi_pembelian)
  
  dataset_prediksi['prediksi'] = np.around(true_dataset_prediksi_pembelian)
  
  jumlah = len(dataset_prediksi) - 9
  datafix = dataset_prediksi[jumlah:]
  datafix = datafix.reset_index()
  print(datafix)
  
  mydb = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="db_smartsys"
  )
  
  id = sys.argv[3]
  namaprediksi = sys.argv[4]
  
  print(id)
  print(namaprediksi)
  
  sql_cek = "SELECT * FROM tb_prakiraan WHERE id_barang = %s"
  cursor = mydb.cursor()
  cursor.execute(sql_cek, (id,))
  nilaidb = cursor.fetchall()
  
  if nilaidb == []:
    sql_insert = "INSERT INTO tb_prakiraan (id_barang, nama_prakiraan) VALUES (%s, %s)"
    record_insert = (id, namaprediksi)
    cursor = mydb.cursor()
    cursor.execute(sql_insert, record_insert)
    mydb.commit()
    
  sql_cek_prakiraan = "SELECT * FROM tb_prakiraan WHERE id_barang = %s"
  cursor = mydb.cursor()
  cursor.execute(sql_cek_prakiraan, (id,))
  nilaiidpra = cursor.fetchall()
  
  idprakiraan = nilaiidpra[0][0]
  
  for index,row in datafix.iterrows():
    sql_insert = "INSERT INTO tb_hasil_prakiraan (id_prakiraan, bulan, hasil_prakiraan) VALUES (%s, %s, %s)"
    record_insert = (idprakiraan, row['tanggal'], int(row['prediksi']))
    cursor = mydb.cursor()
    cursor.execute(sql_insert, record_insert)
    mydb.commit()

if __name__ == '__main__':
  main()